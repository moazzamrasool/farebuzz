<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\AuthorizesLeadAccess;
use App\Http\Controllers\Controller;
use App\Mail\QuotationMail;
use App\Models\PackageEnquiry;
use App\Models\Quotation;
use App\Services\QuotationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class QuotationController extends Controller
{
    use AuthorizesLeadAccess;

    // Dedicated "Create Quotation" page for a lead — pre-filled with the lead's own
    // details/package so the admin only has to fill in the price breakdown.
    public function create(PackageEnquiry $packageEnquiry)
    {
        $this->authorizeVisibility($packageEnquiry);

        $packageEnquiry->load('holidayPackage');

        return view('admin.quotations.create', ['enquiry' => $packageEnquiry]);
    }

    private const STORE_RULES = [
        'customer_name'  => 'required|string|max:255',
        'customer_email' => 'required|email',
        'customer_phone' => 'nullable|string|max:30',
        'valid_until'    => 'nullable|date',
        'notes'          => 'nullable|string',
        'items'                => 'required|array|min:1',
        'items.*.description'  => 'required|string|max:255',
        'items.*.quantity'     => 'required|integer|min:1',
        'items.*.unit_price'   => 'required|numeric',
    ];

    // Renders the exact same PDF "Send Quotation" would email — from the
    // currently-entered form fields, without saving anything — so staff can
    // check it before committing to a quotation number and sending it.
    public function previewDraft(Request $request, PackageEnquiry $packageEnquiry, QuotationService $quotations)
    {
        $this->authorizeVisibility($packageEnquiry);

        $data = $request->validate(self::STORE_RULES);

        return $quotations->previewDraft($data, $packageEnquiry);
    }

    // One button both saves the quotation and emails it to the customer — there's
    // no separate draft/send step.
    public function store(Request $request, PackageEnquiry $packageEnquiry, QuotationService $quotations)
    {
        $this->authorizeVisibility($packageEnquiry);

        $data = $request->validate(self::STORE_RULES);

        $items = collect($data['items'])->values()->map(function ($item, $index) {
            return [
                'description' => $item['description'],
                'quantity'    => (int) $item['quantity'],
                'unit_price'  => (float) $item['unit_price'],
                'amount'      => round($item['quantity'] * $item['unit_price'], 2),
                'sort_order'  => $index,
            ];
        });

        $quotation = DB::transaction(function () use ($data, $items, $packageEnquiry) {
            $quotation = Quotation::create([
                'quotation_number'   => Quotation::generateNumber(),
                'package_enquiry_id' => $packageEnquiry->id,
                'holiday_package_id' => $packageEnquiry->holiday_package_id,
                'created_by'         => Auth::guard('admin')->id(),
                'customer_name'      => $data['customer_name'],
                'customer_email'     => $data['customer_email'],
                'customer_phone'     => $data['customer_phone'] ?? null,
                'valid_until'        => $data['valid_until'] ?? null,
                'notes'              => $data['notes'] ?? null,
                'total_amount'       => $items->sum('amount'),
            ]);

            $quotation->items()->createMany($items->all());

            return $quotation;
        });

        Mail::to($quotation->customer_email)->send(new QuotationMail($quotation));
        $quotation->forceFill(['sent_at' => now()])->save();

        $packageEnquiry->logActivity(
            'quotation',
            "Quotation {$quotation->quotation_number} sent — total ₹".number_format($quotation->total_amount, 2),
            ['quotation_id' => $quotation->id, 'total' => (float) $quotation->total_amount],
            'email'
        );

        return redirect()->route('crm.package-enquiries.show', $packageEnquiry)
            ->with('success', "Quotation {$quotation->quotation_number} sent to the customer.");
    }

    public function show(Quotation $quotation)
    {
        // Route-model binding already applies Quotation's TenantScope (via
        // BelongsToTenant), so a quotation belonging to another company 404s here
        // the same way any other tenant-scoped model does.
        $quotation->load('items', 'packageEnquiry', 'holidayPackage', 'createdBy');

        return view('admin.quotations.show', compact('quotation'));
    }

    public function download(Quotation $quotation, QuotationService $quotations)
    {
        return $quotations->download($quotation);
    }

    // Renders the exact same PDF "Download PDF" produces, inline in the browser.
    public function preview(Quotation $quotation, QuotationService $quotations)
    {
        return $quotations->preview($quotation);
    }
}
