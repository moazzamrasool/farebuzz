<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessEnquiry;
use Illuminate\Support\Str;

class EnquiryController extends Controller
{
    // List all business enquiries
    public function index()
    {
        $enquiries = BusinessEnquiry::latest()->paginate(15);
        return view('admin.enquiries', compact('enquiries'));
    }

    // Approve an enquiry — creates the Business tenant record + unique_id
    public function approve(BusinessEnquiry $enquiry)
    {
        if ($enquiry->status !== 'pending') {
            return redirect()->route('crm.enquiries.index')
                ->with('error', 'This enquiry has already been processed.');
        }

        $business = Business::create([
            'business_enquiry_id' => $enquiry->id,
            'name'                 => $enquiry->business_name,
            'owner_name'           => $enquiry->owner_name,
            'email'                => $enquiry->email,
            'phone'                => $enquiry->phone,
            'business_type'        => $enquiry->business_type,
            'unique_id'            => (string) Str::uuid(),
            'status'               => 'active',
        ]);

        $enquiry->update([
            'status'      => 'approved',
            'business_id' => $business->id,
        ]);

        return redirect()->route('crm.enquiries.index')
            ->with('success', "Business approved. Unique ID: {$business->unique_id}");
    }

    // Reject a pending enquiry
    public function reject(BusinessEnquiry $enquiry)
    {
        if ($enquiry->status !== 'pending') {
            return redirect()->route('crm.enquiries.index')
                ->with('error', 'This enquiry has already been processed.');
        }

        $enquiry->update(['status' => 'rejected']);

        return redirect()->route('crm.enquiries.index')
            ->with('success', 'Enquiry has been rejected.');
    }
}
