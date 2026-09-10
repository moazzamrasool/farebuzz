<?php

namespace App\Http\Controllers;

use App\Mail\LandingEnquiryAdminMail;
use App\Mail\LandingEnquiryThankYouMail;
use App\Models\PackageEnquiry;
use App\Support\SiteTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

// Standalone ad-traffic landing pages — each rendered outside the main site
// layout (see resources/views/layouts/landing.blade.php) with its own route pair.
// Leads are captured into the existing package_enquiries table (PackageEnquiry)
// rather than a parallel table, with holiday_package_id left null and `source`
// identifying which lander it came from.
class LandingController extends Controller
{
    private const KASHMIR_BANGALORE_NAME = 'Kashmir Tour Package from Bangalore';

    public function kashmirBangalore()
    {
        return view('landing.kashmir-bangalore');
    }

    public function kashmirBangaloreEnquiry(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'phone'        => ['required', 'string', 'max:20', 'regex:/^[0-9+\- ]{10,15}$/'],
            'email'        => 'nullable|email|max:150',
            'travel_month' => 'required|string|max:50',
            'travellers'   => 'required|string|max:30',
            // Honeypot: a real visitor never fills this (hidden via CSS); a bot filling
            // every input will.
            'website'      => 'prohibited',
        ]);

        $message = "Package: ".self::KASHMIR_BANGALORE_NAME
            ." | Travel month: {$data['travel_month']}"
            ." | Travellers: {$data['travellers']}"
            ." | Page: {$request->fullUrl()}"
            ." | IP: {$request->ip()}"
            ." | UA: ".mb_strimwidth((string) $request->userAgent(), 0, 200, '');

        try {
            $enquiry = new PackageEnquiry([
                'name'    => $data['name'],
                'phone'   => $data['phone'],
                'email'   => $data['email'] ?? null,
                'source'  => 'kashmir-bangalore-landing',
                'status'  => 'new',
                'message' => $message,
            ]);

            // unique_id is intentionally not mass-assignable (see PackageEnquiry::$fillable),
            // so it must be set as a direct property, not passed into the array above. This
            // landing page has no holiday_package_id/hotel_id for BelongsToTenant to inherit
            // a tenant from, and no admin is logged in on a guest request — without this line
            // the row saves with unique_id NULL and silently disappears from every tenant
            // admin's (non-Super-Admin) CRM lead list.
            $enquiry->unique_id = SiteTenant::id();
            $enquiry->save();
        } catch (\Throwable $e) {
            Log::error('Kashmir landing enquiry save failed: '.$e->getMessage(), ['data' => $data]);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Something went wrong. Please call us instead.'], 500);
            }

            return back()->withInput()->with('error', 'Something went wrong. Please call us instead.');
        }

        // Enquiry is already saved — an SMTP hiccup here shouldn't turn a captured
        // lead into a lost one, so mail failures are logged, not surfaced to the visitor.
        try {
            Mail::to(config('mail.admin_address'))->send(new LandingEnquiryAdminMail($enquiry, self::KASHMIR_BANGALORE_NAME));

            if ($enquiry->email) {
                Mail::to($enquiry->email)->send(new LandingEnquiryThankYouMail($enquiry, self::KASHMIR_BANGALORE_NAME));
            }
        } catch (\Throwable $e) {
            Log::error('Kashmir landing enquiry email failed: '.$e->getMessage(), ['enquiry_id' => $enquiry->id]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => 'Request received. A travel expert will call you during business hours.']);
        }

        return back()->with('success', 'Request received. A travel expert will call you during business hours.');
    }
}
