<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PackageEnquiryController extends Controller
{
    public function index()
    {
        $enquiries = Auth::user()->packageEnquiries()->with('holidayPackage')->latest()->paginate(10);

        return view('user.enquiries.index', compact('enquiries'));
    }
}
