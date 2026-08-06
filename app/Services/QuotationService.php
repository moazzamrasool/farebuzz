<?php

namespace App\Services;

use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;

// Generates the quotation PDF — used for both the admin's direct download and
// as an email attachment. Mirrors InvoiceService's download()/output()/filename() shape.
class QuotationService
{
    public function download(Quotation $quotation)
    {
        return $this->pdf($quotation)->download($this->filename($quotation));
    }

    public function output(Quotation $quotation): string
    {
        return $this->pdf($quotation)->output();
    }

    public function filename(Quotation $quotation): string
    {
        return "quotation-{$quotation->quotation_number}.pdf";
    }

    private function pdf(Quotation $quotation)
    {
        $quotation->loadMissing('items', 'holidayPackage.itineraries');

        return Pdf::loadView('pdf.quotation', compact('quotation'))->setPaper('a4');
    }
}
