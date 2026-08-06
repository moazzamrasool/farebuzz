<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\UserEmailVerificationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        // Customers are a single shared registration pool (see App\Models\User) —
        // every registered user shows up here regardless of company, since
        // registration isn't scoped to a tenant. Each company's own business with
        // a customer (bookings/enquiries) still stays scoped on the detail page.
        $customers = User::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('verified'), function ($q) use ($request) {
                $request->verified === 'verified'
                    ? $q->whereNotNull('email_verified_at')
                    : $q->whereNull('email_verified_at');
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        $tenantId = $this->tenantId();

        $bookings = $customer->bookings()
            ->when($tenantId, fn ($q) => $q->where('unique_id', $tenantId))
            ->latest()
            ->get();

        $enquiries = $customer->packageEnquiries()
            ->when($tenantId, fn ($q) => $q->where('unique_id', $tenantId))
            ->latest()
            ->get();

        return view('admin.customers.show', compact('customer', 'bookings', 'enquiries'));
    }

    public function verify(User $customer)
    {
        // Direct property assignment, not ->update([...]) — status/email_verified_at/
        // email_verification_token are deliberately not in User::$fillable (they must
        // never be settable via mass assignment from user-facing forms), so a mass
        // ->update() call here would silently no-op these fields.
        $customer->email_verified_at        = now();
        $customer->status                   = 'active';
        $customer->email_verification_token = null;
        $customer->save();

        return redirect()->route('crm.customers.show', $customer)
            ->with('success', 'Customer has been manually verified.');
    }

    public function toggleStatus(User $customer)
    {
        $customer->status = $customer->status === 'suspended' ? 'active' : 'suspended';
        $customer->save();

        return redirect()->route('crm.customers.show', $customer)
            ->with('success', 'Customer status updated.');
    }

    public function resendVerification(User $customer)
    {
        if (!$customer->isVerified()) {
            $customer->email_verification_token = Str::random(64);
            $customer->save();

            Mail::to($customer->email)->send(new UserEmailVerificationMail($customer));
        }

        return redirect()->route('crm.customers.show', $customer)
            ->with('success', 'Verification email sent.');
    }

    private function tenantId(): ?string
    {
        $admin = Auth::guard('admin')->user();

        return $admin->isSuperAdmin() ? null : $admin->unique_id;
    }
}
