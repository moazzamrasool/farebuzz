<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Support\Carbon;

// Single source of truth for coupon validation and discount math — used both by the
// public "Apply" AJAX preview (CouponController@apply) and, again, authoritatively
// inside BookingController::store()/hotelStore() right before the booking (and its
// total_amount, which PayU reads verbatim) is persisted. The preview call and the
// authoritative call must never diverge, so both funnel through evaluate() below
// rather than each re-implementing the rules.
class CouponService
{
    // Looks up an active-or-not coupon for this tenant by code (case-insensitive) —
    // deliberately not pre-filtered by status/dates so evaluate() can return a
    // specific reason ("expired", "inactive") instead of a generic "not found".
    public function find(string $code): ?Coupon
    {
        return Coupon::forSite()
            ->whereRaw('UPPER(code) = ?', [strtoupper(trim($code))])
            ->first();
    }

    /**
     * Validates $code against a booking of $bookingType ('package'|'hotel') for
     * item $itemId (holiday_package_id or hotel_id), given the booking's own
     * pre-discount subtotal. Returns:
     *   ['valid' => bool, 'message' => ?string, 'coupon' => ?Coupon, 'discount' => float]
     */
    public function evaluate(
        string $code,
        string $bookingType,
        ?int $itemId,
        float $subtotal,
        ?int $userId,
        ?string $email
    ): array {
        $coupon = $this->find($code);

        if (!$coupon) {
            return $this->invalid('This coupon code is not valid.');
        }

        if ($coupon->status !== 'active') {
            return $this->invalid('This coupon is no longer active.');
        }

        $today = Carbon::today();
        if (!$today->between($coupon->valid_from, $coupon->valid_to)) {
            return $this->invalid('This coupon has expired or is not yet valid.');
        }

        if ($subtotal < (float) $coupon->min_booking_amount) {
            return $this->invalid('This coupon requires a minimum booking amount of ₹'.number_format((float) $coupon->min_booking_amount, 2).'.');
        }

        if (!$this->isApplicable($coupon, $bookingType, $itemId)) {
            return $this->invalid('This coupon is not applicable to this booking.');
        }

        if ($coupon->usage_limit !== null && CouponUsage::where('coupon_id', $coupon->id)->count() >= $coupon->usage_limit) {
            return $this->invalid('This coupon has reached its usage limit.');
        }

        if ($coupon->per_user_limit !== null) {
            $usedByUser = CouponUsage::where('coupon_id', $coupon->id)
                ->where(function ($query) use ($userId, $email) {
                    $query->when($userId, fn ($q) => $q->orWhere('user_id', $userId))
                        ->when($email, fn ($q) => $q->orWhere('email', $email));
                })
                ->count();

            if ($usedByUser >= $coupon->per_user_limit) {
                return $this->invalid('You have already used this coupon the maximum number of times.');
            }
        }

        $discount = $this->calculateDiscount($coupon, $subtotal);

        if ($discount <= 0) {
            return $this->invalid('This coupon does not apply any discount to this booking.');
        }

        return [
            'valid' => true,
            'message' => null,
            'coupon' => $coupon,
            'discount' => $discount,
        ];
    }

    // Folds a coupon discount into an existing calculateBreakdown()/calculateHotelBreakdown()
    // array, re-deriving tax + total exactly like those methods do — tax is charged on the
    // subtotal AFTER the coupon discount, same convention already used for the room-type discount.
    public function applyToBreakdown(array $breakdown, float $discount): array
    {
        $subtotal = $breakdown['base_fare'] - $breakdown['discount_amount'] + $breakdown['activities_total'] + $breakdown['hotels_total'];
        $subtotalAfterCoupon = max(0, $subtotal - $discount);
        $taxesFee = round($subtotalAfterCoupon * 0.05, 2);

        $breakdown['coupon_discount_amount'] = round($discount, 2);
        $breakdown['taxes_fee'] = $taxesFee;
        $breakdown['total_amount'] = round($subtotalAfterCoupon + $taxesFee, 2);

        return $breakdown;
    }

    private function calculateDiscount(Coupon $coupon, float $subtotal): float
    {
        if ($coupon->discount_type === 'percentage') {
            $discount = $subtotal * ((float) $coupon->discount_value / 100);
            if ($coupon->max_discount_amount !== null) {
                $discount = min($discount, (float) $coupon->max_discount_amount);
            }
        } else {
            $discount = (float) $coupon->discount_value;
        }

        // Never let a coupon push the total below zero.
        return round(min($discount, $subtotal), 2);
    }

    private function isApplicable(Coupon $coupon, string $bookingType, ?int $itemId): bool
    {
        return match ($coupon->applicable_to) {
            'all' => true,
            'packages' => $bookingType === 'package',
            'hotels' => $bookingType === 'hotel',
            'specific_packages' => $bookingType === 'package' && $itemId && in_array($itemId, $coupon->applicable_ids ?? [], false),
            'specific_hotels' => $bookingType === 'hotel' && $itemId && in_array($itemId, $coupon->applicable_ids ?? [], false),
            default => false,
        };
    }

    private function invalid(string $message): array
    {
        return ['valid' => false, 'message' => $message, 'coupon' => null, 'discount' => 0.0];
    }
}
