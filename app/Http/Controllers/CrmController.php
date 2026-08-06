<?php

namespace App\Http\Controllers;

use App\Models\BusinessEnquiry;
use Illuminate\Http\Request;

class CrmController extends Controller
{
    // CRM marketing/landing page — public
    public function landing()
    {
        return view('crm');
    }

    // Submit a business enquiry from the landing page
    public function storeEnquiry(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'owner_name'    => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => 'required|string|max:20',
            'business_type' => 'nullable|string|max:255',
            'message'       => 'nullable|string',
        ]);

        BusinessEnquiry::create([
            'business_name' => $request->business_name,
            'owner_name'    => $request->owner_name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'business_type' => $request->business_type,
            'message'       => $request->message,
            'status'        => 'pending',
        ]);

        return response()->json(['success' => 'Your enquiry has been submitted successfully! Our team will get back to you soon.']);
    }
}
