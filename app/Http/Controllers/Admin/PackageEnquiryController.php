<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LeadStatus;
use App\Http\Controllers\Admin\Concerns\AuthorizesLeadAccess;
use App\Http\Controllers\Controller;
use App\Mail\LeadItineraryMail;
use App\Models\Admin\Admin;
use App\Models\PackageEnquiry;
use App\Services\ItineraryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

// Tenant-scoped leads (TenantScope on PackageEnquiry already filters by the logged-in
// admin's unique_id, or shows every tenant for a Super Admin) — distinct from the
// Super-Admin-only Business Enquiries module.
class PackageEnquiryController extends Controller
{
    use AuthorizesLeadAccess;

    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $enquiries = PackageEnquiry::query()
            ->visibleTo($admin)
            ->status($request->status)
            ->source($request->source)
            ->assignedTo($request->assigned_admin_id)
            ->betweenDates($request->date_from, $request->date_to)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->with(['holidayPackage', 'assignedAdmin', 'pendingFollowUp'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $statusOptions = LeadStatus::options();
        $agents = Admin::whereIn('tier', ['admin', 'user'])->orderBy('name')->get();
        $sources = PackageEnquiry::query()->whereNotNull('source')->distinct()->pluck('source');

        return view('admin.package-enquiries.index', compact('enquiries', 'statusOptions', 'agents', 'sources'));
    }

    public function show(PackageEnquiry $packageEnquiry, ItineraryService $itineraries)
    {
        $this->authorizeVisibility($packageEnquiry);

        $packageEnquiry->load(['holidayPackage', 'assignedAdmin', 'activities.admin', 'followUps' => fn ($q) => $q->orderByDesc('due_at')]);

        $statusOptions = LeadStatus::options();
        $lostStatuses = LeadStatus::requiringLostReason();
        $agents = Admin::whereIn('tier', ['admin', 'user'])->orderBy('name')->get();
        $hasItinerary = $itineraries->availableForLead($packageEnquiry);

        return view('admin.package-enquiries.show', [
            'enquiry' => $packageEnquiry,
            'statusOptions' => $statusOptions,
            'lostStatuses' => $lostStatuses,
            'agents' => $agents,
            'hasItinerary' => $hasItinerary,
        ]);
    }

    public function updateStatus(Request $request, PackageEnquiry $packageEnquiry)
    {
        $this->authorizeVisibility($packageEnquiry);

        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_column(LeadStatus::cases(), 'value'))],
            'lost_reason' => 'nullable|required_if:status,lost,junk|string',
        ]);

        $packageEnquiry->changeStatus(LeadStatus::from($data['status']), $data['lost_reason'] ?? null);

        return back()->with('success', 'Lead status updated.');
    }

    public function sendItinerary(PackageEnquiry $packageEnquiry, ItineraryService $itineraries)
    {
        $this->authorizeVisibility($packageEnquiry);

        abort_unless($itineraries->availableForLead($packageEnquiry), 404, 'No itinerary is available for this enquiry\'s package.');

        Mail::to($packageEnquiry->email)->send(new LeadItineraryMail($packageEnquiry));

        $packageEnquiry->logActivity('email', 'Itinerary PDF sent', ['to' => $packageEnquiry->email], 'email');

        return back()->with('success', 'Itinerary emailed to the lead.');
    }

    public function destroy(PackageEnquiry $packageEnquiry)
    {
        $this->authorizeVisibility($packageEnquiry);

        $packageEnquiry->delete();

        return redirect()->route('crm.package-enquiries.index')->with('success', 'Enquiry deleted successfully.');
    }
}
