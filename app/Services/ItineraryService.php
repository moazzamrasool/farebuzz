<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\HolidayPackage;
use App\Models\PackageEnquiry;
use Barryvdh\DomPDF\Facade\Pdf;

// Generates the day-by-day itinerary PDF (from PackageItinerary rows on the
// package being booked/enquired about) — used for both the customer's own
// download and as an admin-triggered email attachment. Mirrors InvoiceService's
// download()/output()/filename() shape.
class ItineraryService
{
    public function downloadForBooking(Booking $booking)
    {
        return $this->pdfForPackage($this->packageFor($booking), [
            'referenceLabel' => 'Booking Reference',
            'referenceValue' => $booking->booking_reference,
            'travellerName'  => $booking->traveller_name,
            'travelDate'     => $booking->travel_date,
        ])->download($this->filenameForBooking($booking));
    }

    public function outputForBooking(Booking $booking): string
    {
        return $this->pdfForPackage($this->packageFor($booking), [
            'referenceLabel' => 'Booking Reference',
            'referenceValue' => $booking->booking_reference,
            'travellerName'  => $booking->traveller_name,
            'travelDate'     => $booking->travel_date,
        ])->output();
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
        return $this->pdfForPackage($this->packageFor($enquiry), [
            'referenceLabel' => 'Enquiry Reference',
            'referenceValue' => '#'.$enquiry->id,
            'travellerName'  => $enquiry->name,
            'travelDate'     => $enquiry->travel_date,
        ])->download($this->filenameForLead($enquiry));
    }

    public function outputForLead(PackageEnquiry $enquiry): string
    {
        return $this->pdfForPackage($this->packageFor($enquiry), [
            'referenceLabel' => 'Enquiry Reference',
            'referenceValue' => '#'.$enquiry->id,
            'travellerName'  => $enquiry->name,
            'travelDate'     => $enquiry->travel_date,
        ])->output();
    }

    public function filenameForLead(PackageEnquiry $enquiry): string
    {
        return "itinerary-enquiry-{$enquiry->id}.pdf";
    }

    public function availableForLead(PackageEnquiry $enquiry): bool
    {
        return $this->packageFor($enquiry) !== null;
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

        return Pdf::loadView('pdf.package_itinerary', array_merge($meta, [
            'package' => $package,
        ]))->setPaper('a4');
    }
}
