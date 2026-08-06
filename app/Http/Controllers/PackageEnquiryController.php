<?php

namespace App\Http\Controllers;

use App\Mail\PackageEnquiryAdminMail;
use App\Mail\PackageEnquiryCustomerMail;
use App\Models\HolidayPackage;
use App\Models\PackageEnquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

// Public "Enquire Now" flow — available regardless of the package's booking_type.
class PackageEnquiryController extends Controller
{
    public function store(Request $request, HolidayPackage $package)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone'        => 'required|string|max:20',
            'travel_date'  => 'nullable|date',
            'travellers'   => 'nullable|integer|min:1|max:50',
            'message'      => 'nullable|string|max:2000',
        ]);

        $enquiry = PackageEnquiry::create([
            'holiday_package_id' => $package->id,
            'user_id'            => Auth::id(),
            'status'             => 'new',
            ...$data,
        ]);

        // Email delivery is best-effort — the enquiry is already saved, so an SMTP
        // hiccup shouldn't turn into a 500 and hide a successfully captured lead.
        try {
            Mail::to($enquiry->email)->send(new PackageEnquiryCustomerMail($enquiry));
            Mail::to(config('mail.admin_address'))->send(new PackageEnquiryAdminMail($enquiry));
        } catch (\Throwable $e) {
            Log::error('Package enquiry email failed: '.$e->getMessage(), ['enquiry_id' => $enquiry->id]);
        }

        return response()->json([
            'success'  => 'Your enquiry has been submitted successfully!',
            'redirect' => route('packages.enquire.thankyou', $package->slug),
        ]);
    }

    public function thankYou(HolidayPackage $package)
    {
        return view('packages.enquiry_thank_you', compact('package'));
    }
}
