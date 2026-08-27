<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\HolidayPackage;
use App\Models\PackageEnquiry;
use App\Support\PdfPageNumbers;
use Barryvdh\DomPDF\Facade\Pdf;

// Generates the day-by-day itinerary PDF (from PackageItinerary rows on the
// package being booked/enquired about) — used for both the customer's own
// download and as an admin-triggered email attachment. Mirrors InvoiceService's
// download()/output()/filename() shape. previewFor*() render the exact same PDF
// inline (dompdf stream() vs. download()) so a "Preview" button always shows
// staff precisely what the "Send" action will deliver.
class ItineraryService
{
    public function downloadForBooking(Booking $booking)
    {
        return $this->pdfForPackage($this->packageFor($booking), $this->metaForBooking($booking))
            ->download($this->filenameForBooking($booking));
    }

    public function previewForBooking(Booking $booking)
    {
        return $this->pdfForPackage($this->packageFor($booking), $this->metaForBooking($booking))
            ->stream($this->filenameForBooking($booking));
    }

    public function outputForBooking(Booking $booking): string
    {
        return $this->pdfForPackage($this->packageFor($booking), $this->metaForBooking($booking))->output();
    }

    public function filenameForBooking(Booking $booking): string
    {
        return "itinerary-{$booking->booking_reference}.pdf";
    }

    public function availableForBooking(Booking $booking): bool
    {
        return $booking->booking_type !== 'hotel' && $this->packageFor($booking) !== null;
    }

    public function downloadForLead(PackageEnquiry $enquiry)
    {
        return $this->pdfForPackage($this->packageFor($enquiry), $this->metaForLead($enquiry))
            ->download($this->filenameForLead($enquiry));
    }

    public function previewForLead(PackageEnquiry $enquiry)
    {
        return $this->pdfForPackage($this->packageFor($enquiry), $this->metaForLead($enquiry))
            ->stream($this->filenameForLead($enquiry));
    }

    public function outputForLead(PackageEnquiry $enquiry): string
    {
        return $this->pdfForPackage($this->packageFor($enquiry), $this->metaForLead($enquiry))->output();
    }

    public function filenameForLead(PackageEnquiry $enquiry): string
    {
        return "itinerary-enquiry-{$enquiry->id}.pdf";
    }

    public function availableForLead(PackageEnquiry $enquiry): bool
    {
        return $this->packageFor($enquiry) !== null;
    }

    private function metaForBooking(Booking $booking): array
    {
        return [
            'referenceLabel' => 'Booking Reference',
            'referenceValue' => $booking->booking_reference,
            'travellerName'  => $booking->traveller_name,
            'travelDate'     => $booking->travel_date,
        ];
    }

    private function metaForLead(PackageEnquiry $enquiry): array
    {
        return [
            'referenceLabel' => 'Enquiry Reference',
            'referenceValue' => '#'.$enquiry->id,
            'travellerName'  => $enquiry->name,
            'travelDate'     => $enquiry->travel_date,
        ];
    }

    private function packageFor(Booking|PackageEnquiry $model): ?HolidayPackage
    {
        if (!$model->holiday_package_id) {
            return null;
        }

        $package = $model->holidayPackage()->with('itineraries')->first();

        return $package && $package->itineraries->isNotEmpty() ? $package : null;
    }

    private function pdfForPackage(?HolidayPackage $package, array $meta)
    {
        abort_if(!$package, 404, 'No itinerary is available for this package.');

        if (config('itinerary.pdf_template') === 'premium') {
            $data = app(PremiumItineraryPresenter::class)->present($package, $meta);
            $pdf = Pdf::loadView('pdf.premium.itinerary', $data)->setPaper('a4', 'landscape');

            return PdfPageNumbers::apply($pdf);
        }

        return Pdf::loadView('pdf.package_itinerary', array_merge($meta, [
            'package' => $package,
        ]))->setPaper('a4');
    }
}
