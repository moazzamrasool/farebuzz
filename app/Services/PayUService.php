<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

// Isolated PayU India integration — nothing else in the app talks to PayU directly.
// Switching from test to live is purely a config change (PAYU_MODE + live key/salt),
// no code here changes.
class PayUService
{
    // The 11 "hashable" fields PayU's request/response hash formulas revolve around,
    // in the exact order their docs specify (udf6-udf10 are unused, always empty).
    private const UDF_COUNT = 10;

    // PayU's S2S "Verify Payment" API — used by the admin reconcile action to ask PayU
    // directly what happened to a txnid whose browser callback never arrived (network
    // drop between PayU and the customer, server restart mid-callback, etc). Same for
    // both test and live merchants — PayU keys the lookup by key+txnid, not by host.
    private const VERIFY_API_URL = 'https://info.payu.in/merchant/postservice?form=2';

    public function currentMode(): string
    {
        return config('services.payu.mode', 'test');
    }

    private function credentials(string $mode): array
    {
        $key = config("services.payu.{$mode}.key");
        $salt = config("services.payu.{$mode}.salt");
        $url = config("services.payu.{$mode}.url");

        if (blank($key) || blank($salt)) {
            // Fail loudly and immediately rather than silently posting a form to PayU
            // with an empty key/salt — that would build a hash PayU can never accept
            // and only surface as a confusing "invalid hash" bounce for the customer.
            throw new \RuntimeException("PayU {$mode} credentials are not configured (PAYU_{$this->envPrefix($mode)}_KEY / _SALT).");
        }

        return compact('key', 'salt', 'url');
    }

    private function envPrefix(string $mode): string
    {
        return strtoupper($mode);
    }

    // Builds the PayU checkout form params for $booking and logs a "initiated" BookingPayment.
    // Returns ['url' => ..., 'params' => [...]] — the caller auto-submits a hidden form to $url.
    public function initiate(Booking $booking): array
    {
        $mode = $this->currentMode();
        ['key' => $key, 'salt' => $salt, 'url' => $url] = $this->credentials($mode);

        $txnid = $this->generateTxnId();
        $amount = number_format((float) $booking->total_amount, 2, '.', '');
        $productinfo = Str::limit($booking->package_title, 90, '');
        $firstname = $booking->traveller_name;
        $email = $booking->traveller_email;
        $phone = $booking->traveller_phone;
        $surl = route('payu.callback.success');
        $furl = route('payu.callback.failure');

        $hash = $this->buildRequestHash($key, $txnid, $amount, $productinfo, $firstname, $email, $salt);

        BookingPayment::create([
            'unique_id'      => $booking->unique_id,
            'booking_id'     => $booking->id,
            'gateway'        => 'payu',
            'mode'           => $mode,
            'gateway_txn_id' => $txnid,
            'amount'         => $booking->total_amount,
            'currency'       => 'INR',
            'status'         => 'initiated',
            // Never store the salt/hash — only the outgoing, non-secret request fields.
            'request_payload' => compact('key', 'txnid', 'amount', 'productinfo', 'firstname', 'email', 'phone', 'surl', 'furl'),
        ]);

        return [
            'url' => $url,
            'params' => [
                'key' => $key,
                'txnid' => $txnid,
                'amount' => $amount,
                'productinfo' => $productinfo,
                'firstname' => $firstname,
                'email' => $email,
                'phone' => $phone,
                'surl' => $surl,
                'furl' => $furl,
                'hash' => $hash,
                'service_provider' => 'payu_paisa',
            ],
        ];
    }

    // Recomputes PayU's reverse hash formula and compares it (constant-time) against
    // what PayU sent back. This is the ONLY thing that should be trusted to decide
    // success — never the "status" field alone, which an attacker could forge.
    public function verifyResponse(array $response, string $mode): bool
    {
        ['salt' => $salt] = $this->credentials($mode);

        if (empty($response['hash'])) {
            return false;
        }

        $expected = $this->buildResponseHash(
            $response['key'] ?? '',
            $response['txnid'] ?? '',
            $response['amount'] ?? '',
            $response['productinfo'] ?? '',
            $response['firstname'] ?? '',
            $response['email'] ?? '',
            $response['status'] ?? '',
            $salt
        );

        return hash_equals($expected, (string) $response['hash']);
    }

    // Single funnel for BOTH the success and failure callback routes — deciding whether
    // a booking gets confirmed is entirely this method's job, never the controller's.
    // Returns ['status' => ..., 'booking' => ?Booking]:
    //   'confirmed'        — freshly marked paid this call; caller should send emails.
    //   'already_confirmed'— a prior call already paid this booking (replay, or a late
    //                        callback from an older/abandoned attempt) — no side effects
    //                        re-run, and the booking is never downgraded.
    //   'failed'           — genuine, hash-verified PayU failure/cancel/amount-mismatch.
    //   'unverified'       — hash didn't check out; booking/payment left untouched so a
    //                        forged request (e.g. a guessed txnid POSTed to the failure
    //                        URL) can never flip a real booking's status either way.
    //   'unknown'          — no matching payment for this txnid at all.
    public function processCallback(array $response, bool $expectingSuccess): array
    {
        $txnid = $response['txnid'] ?? null;
        $payment = $txnid ? BookingPayment::where('gateway_txn_id', $txnid)->first() : null;

        if (!$payment) {
            return ['status' => 'unknown', 'booking' => null];
        }

        $booking = $payment->booking;

        // Once paid — by this exact attempt or an earlier retry on the same booking —
        // nothing may change that: not a replayed success callback, and not a forged or
        // late-arriving hit on the failure URL carrying an older, abandoned txnid.
        if ($payment->status === 'success' || $booking->payment_status === 'paid') {
            return ['status' => 'already_confirmed', 'booking' => $booking];
        }

        $verified = $this->verifyResponse($response, $payment->mode);

        if (!$verified) {
            Log::warning('PayU callback hash verification failed — booking left untouched.', [
                'txnid' => $txnid,
                'route' => $expectingSuccess ? 'success' : 'failure',
            ]);

            return ['status' => 'unverified', 'booking' => $booking];
        }

        $payuSaysSuccess = ($response['status'] ?? '') === 'success';
        $amountMatches = $this->amountMatches($response['amount'] ?? null, $payment->amount);

        if ($expectingSuccess && $payuSaysSuccess && $amountMatches) {
            return ['status' => 'confirmed', 'booking' => $this->markSuccess($payment, $response)];
        }

        if ($payuSaysSuccess && !$amountMatches) {
            // Hash checked out (so this really is PayU) but the amount it's confirming
            // doesn't match what we charged this booking for — never confirm on that.
            Log::error('PayU callback amount mismatch — refusing to confirm.', [
                'txnid' => $txnid,
                'expected_amount' => $payment->amount,
                'gateway_amount' => $response['amount'] ?? null,
            ]);
        }

        return ['status' => 'failed', 'booking' => $this->markFailed($payment, $response, true)];
    }

    // Asks PayU directly what happened to $payment's txnid — the recovery path for
    // "customer paid but our callback never arrived" (network drop, server restart,
    // etc). Used by the admin "Verify with PayU" action, never by the public flow.
    public function reconcileWithGateway(BookingPayment $payment): array
    {
        ['key' => $key, 'salt' => $salt] = $this->credentials($payment->mode);

        $command = 'verify_payment';
        $hash = hash('sha512', "{$key}|{$command}|{$payment->gateway_txn_id}|{$salt}");

        try {
            $response = Http::asForm()->timeout(15)->post(self::VERIFY_API_URL, [
                'key' => $key,
                'command' => $command,
                'var1' => $payment->gateway_txn_id,
                'hash' => $hash,
            ]);
        } catch (\Throwable $e) {
            Log::error('PayU reconcile request failed: '.$e->getMessage(), ['txnid' => $payment->gateway_txn_id]);

            return ['ok' => false, 'message' => 'Could not reach PayU right now. Try again shortly.'];
        }

        if (!$response->successful()) {
            Log::error('PayU reconcile call returned an error status.', [
                'txnid' => $payment->gateway_txn_id,
                'http_status' => $response->status(),
            ]);

            return ['ok' => false, 'message' => 'PayU returned an unexpected response.'];
        }

        $txn = $response->json('transaction_details.'.$payment->gateway_txn_id);

        if (!$txn) {
            return ['ok' => false, 'message' => 'PayU has no record of this transaction (yet).'];
        }

        return ['ok' => true, 'gateway_status' => $txn['status'] ?? null, 'raw' => $txn];
    }

    // Applies a gateway-verified reconcile result the same way processCallback() would
    // have from a live callback — same idempotency guard, same amount check, same
    // markSuccess()/markFailed() so every path a booking can get confirmed through
    // shares one rulebook.
    public function confirmFromReconciliation(BookingPayment $payment, array $txn): array
    {
        $booking = $payment->booking;

        if ($payment->status === 'success' || $booking->payment_status === 'paid') {
            return ['status' => 'already_confirmed', 'booking' => $booking];
        }

        $gatewayStatus = $txn['status'] ?? null;
        $gatewayAmount = $txn['amt'] ?? $txn['amount'] ?? null;
        $amountMatches = $this->amountMatches($gatewayAmount, $payment->amount);

        if ($gatewayStatus === 'success' && $amountMatches) {
            $response = [
                'txnid' => $payment->gateway_txn_id,
                'mihpayid' => $txn['mihpayid'] ?? null,
                'status' => 'success',
                'reconciled_at' => now()->toIso8601String(),
            ];

            return ['status' => 'confirmed', 'booking' => $this->markSuccess($payment, $response)];
        }

        if ($gatewayStatus === 'success' && !$amountMatches) {
            Log::error('PayU reconcile amount mismatch — refusing to confirm.', [
                'txnid' => $payment->gateway_txn_id,
                'expected_amount' => $payment->amount,
                'gateway_amount' => $gatewayAmount,
            ]);

            return ['status' => 'mismatch', 'booking' => $booking];
        }

        return ['status' => 'still_pending', 'booking' => $booking, 'gateway_status' => $gatewayStatus];
    }

    // Tolerant compare — PayU/our own decimals can differ in trailing-zero formatting,
    // but never by more than a paisa's rounding.
    private function amountMatches(mixed $gatewayAmount, mixed $storedAmount): bool
    {
        if ($gatewayAmount === null || $gatewayAmount === '') {
            return false;
        }

        return abs((float) $gatewayAmount - (float) $storedAmount) < 0.01;
    }

    // Marks the matching BookingPayment + Booking as paid/confirmed. Only ever reached
    // after processCallback()/confirmFromReconciliation() have verified hash + amount.
    private function markSuccess(BookingPayment $payment, array $response): Booking
    {
        $payment->update([
            'status' => 'success',
            'gateway_payment_id' => $response['mihpayid'] ?? null,
            'response_payload' => $response,
            'hash_verified' => true,
        ]);

        $booking = $payment->booking;
        $booking->update(['status' => 'confirmed', 'payment_status' => 'paid']);

        $this->recordCouponUsage($booking);

        return $booking->fresh();
    }

    // Usage is only ever recorded here — after a verified, successful payment — so a
    // failed or abandoned PayU attempt never consumes a coupon's usage/per-user limit;
    // handleFailure() needs no matching "rollback" because nothing was ever incremented.
    // The row lock closes the (already extremely narrow) race between two simultaneous
    // successful payments both passing the usage_limit check at once.
    private function recordCouponUsage(Booking $booking): void
    {
        if (!$booking->coupon_id) {
            return;
        }

        DB::transaction(function () use ($booking) {
            $coupon = Coupon::withoutTenantScope()->whereKey($booking->coupon_id)->lockForUpdate()->first();

            if (!$coupon) {
                return;
            }

            $alreadyRecorded = CouponUsage::where('coupon_id', $coupon->id)->where('booking_id', $booking->id)->exists();
            $withinLimit = !$coupon->usage_limit || CouponUsage::where('coupon_id', $coupon->id)->count() < $coupon->usage_limit;

            if (!$alreadyRecorded && $withinLimit) {
                CouponUsage::create([
                    'unique_id' => $booking->unique_id,
                    'coupon_id' => $coupon->id,
                    'booking_id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'email' => $booking->traveller_email,
                    'discount_amount' => $booking->coupon_discount_amount,
                ]);
            }
        });
    }

    // Marks the matching BookingPayment + Booking as failed. Only reached for a
    // hash-verified PayU failure/cancel or a verified-but-amount-mismatched response —
    // callers must never route an unverified response here (see processCallback()).
    private function markFailed(BookingPayment $payment, array $response, bool $hashVerified): Booking
    {
        $payment->update([
            'status' => 'failed',
            'response_payload' => $response,
            'hash_verified' => $hashVerified,
        ]);

        $booking = $payment->booking;
        $booking->update(['status' => 'failed', 'payment_status' => 'failed']);

        return $booking->fresh();
    }

    private function generateTxnId(): string
    {
        do {
            $txnid = 'PAYU'.now()->format('YmdHis').Str::upper(Str::random(6));
        } while (BookingPayment::where('gateway_txn_id', $txnid)->exists());

        return $txnid;
    }

    // key|txnid|amount|productinfo|firstname|email|udf1..udf10|salt (udf1-10 empty — unused).
    private function buildRequestHash(string $key, string $txnid, string $amount, string $productinfo, string $firstname, string $email, string $salt): string
    {
        $fields = array_merge(
            [$key, $txnid, $amount, $productinfo, $firstname, $email],
            array_fill(0, self::UDF_COUNT, ''),
            [$salt]
        );

        return hash('sha512', implode('|', $fields));
    }

    // Reverse of the request hash: salt|status|udf10..udf1|email|firstname|productinfo|amount|txnid|key.
    private function buildResponseHash(string $key, string $txnid, string $amount, string $productinfo, string $firstname, string $email, string $status, string $salt): string
    {
        $fields = array_merge(
            [$salt, $status],
            array_fill(0, self::UDF_COUNT, ''),
            [$email, $firstname, $productinfo, $amount, $txnid, $key]
        );

        return hash('sha512', implode('|', $fields));
    }
}
