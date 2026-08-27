<?php

namespace App\Services;

use App\Models\PackageEnquiry;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Support\PdfImage;
use App\Support\PdfPageNumbers;
use Barryvdh\DomPDF\Facade\Pdf;

// Generates the quotation PDF — used for both the admin's direct download and
// as an email attachment. Mirrors InvoiceService's download()/output()/filename()
// shape. preview()/previewDraft() render the exact same PDF inline (dompdf
// stream() vs. download()) so a "Preview" button always shows staff precisely
// what the "Send"/"Confirm & Send" action will deliver — previewDraft() does so
// from freshly-validated create-form data, before anything is saved.
class QuotationService
{
    public function download(Quotation $quotation)
    {
        return $this->pdf($quotation)->download($this->filename($quotation));
    }

    public function preview(Quotation $quotation)
    {
        return $this->pdf($quotation)->stream($this->filename($quotation));
    }

    public function output(Quotation $quotation): string
    {
        return $this->pdf($quotation)->output();
    }

    public function filename(Quotation $quotation): string
    {
        return "quotation-{$quotation->quotation_number}.pdf";
    }

    public function previewDraft(array $data, PackageEnquiry $packageEnquiry)
    {
        $items = collect($data['items'])->values()->map(fn ($item, $index) => new QuotationItem([
            'description' => $item['description'],
            'quantity'    => (int) $item['quantity'],
            'unit_price'  => (float) $item['unit_price'],
            'amount'      => round($item['quantity'] * $item['unit_price'], 2),
            'sort_order'  => $index,
        ]));

        $quotation = new Quotation([
            'quotation_number'   => 'PREVIEW',
            'package_enquiry_id' => $packageEnquiry->id,
            'holiday_package_id' => $packageEnquiry->holiday_package_id,
            'customer_name'      => $data['customer_name'],
            'customer_email'     => $data['customer_email'],
            'customer_phone'     => $data['customer_phone'] ?? null,
            'valid_until'        => $data['valid_until'] ?? null,
            'notes'              => $data['notes'] ?? null,
            'total_amount'       => $items->sum('amount'),
        ]);
        $quotation->created_at = now();
        $quotation->setRelation('items', $items);
        $quotation->setRelation('holidayPackage', $packageEnquiry->holidayPackage);

        return $this->pdf($quotation)->stream('quotation-preview.pdf');
    }

    private function pdf(Quotation $quotation)
    {
        $quotation->loadMissing('items', 'holidayPackage.itineraries');

        if (config('itinerary.pdf_template') === 'premium') {
            $package = $quotation->holidayPackage;

            $data = $package
                ? app(PremiumItineraryPresenter::class)->present($package)
                : [
                    'package' => null, 'coverImage' => null, 'days' => [], 'hotels' => [],
                    'inclusions' => [], 'exclusions' => [], 'logo' => PdfImage::resolve('frontend/img/logo.png'),
                ];

            $pdf = Pdf::loadView('pdf.premium.quotation', array_merge($data, ['quotation' => $quotation]))
                ->setPaper('a4', 'landscape');

            return PdfPageNumbers::apply($pdf);
        }

        return Pdf::loadView('pdf.quotation', compact('quotation'))->setPaper('a4');
    }
}
