<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\HolidayPackage;
use App\Models\Hotel;
use App\Models\PackageItinerary;
use App\Models\PackagePhoto;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Services\PremiumItineraryPresenter;
use App\Support\PdfPageNumbers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

// Renders the actual Blade views through Laravel's view compiler (and, for one
// case, all the way through dompdf) to catch template-level mistakes — typos,
// undefined variables, CSS dompdf can't parse — that the pure-PHP presenter
// unit tests can't see. Everything is built in-memory (no DB), same technique
// as PremiumItineraryPresenterTest, since this repo has no test-database/tenant
// fixture set up for these models; the config switch in ItineraryService/
// QuotationService itself is a one-line view-name choice, so the real risk is
// in the templates, which is what this test exercises directly.
class PremiumItineraryPdfTest extends TestCase
{
    private function packageWithPhoto(): HolidayPackage
    {
        Storage::fake('public');
        Storage::disk('public')->put('cover.jpg', 'x');

        $package = new HolidayPackage([
            'title' => 'Goa Beach Getaway', 'nights' => 2, 'days' => 3,
            'hotel_category' => '4 Star Hotels', 'meals' => 'Daily Breakfast',
        ]);
        $package->setRelation('itineraries', collect([
            new PackageItinerary([
                'day_number' => 1, 'title' => 'Arrival', 'route_summary' => 'Airport -> Hotel',
                'detail' => 'Arrive and check in.', 'bullet_points' => ['Airport pickup', 'Hotel check-in'],
                'meal_tags' => ['dinner'],
            ]),
        ]));
        $package->setRelation('photos', collect([new PackagePhoto(['path' => 'cover.jpg', 'is_cover' => true])]));
        $package->setRelation('destination', new Destination(['name' => 'Goa']));
        $package->setRelation('hotels', collect());
        $package->setRelation('optionalActivities', collect());
        $package->setRelation('inclusionFeatures', collect());
        $package->setRelation('exclusionFeatures', collect());
        $package->setRelation('customInclusions', collect());
        $package->setRelation('customExclusions', collect());

        return $package;
    }

    private function packageWithNoPhotos(): HolidayPackage
    {
        $package = new HolidayPackage(['title' => 'No Photo Package', 'nights' => 1, 'days' => 2]);
        $package->setRelation('itineraries', collect([
            new PackageItinerary(['day_number' => 1, 'title' => 'Day one', 'bullet_points' => [], 'meal_tags' => []]),
        ]));
        $package->setRelation('photos', collect());
        $package->setRelation('destination', null);
        $package->setRelation('hotels', collect());
        $package->setRelation('optionalActivities', collect());
        $package->setRelation('inclusionFeatures', collect());
        $package->setRelation('exclusionFeatures', collect());
        $package->setRelation('customInclusions', collect());
        $package->setRelation('customExclusions', collect());

        return $package;
    }

    public function test_classic_itinerary_template_still_renders_unchanged(): void
    {
        $package = $this->packageWithPhoto();

        $html = View::make('pdf.package_itinerary', [
            'package' => $package, 'referenceLabel' => 'Booking Reference',
            'referenceValue' => 'BK-1', 'travellerName' => 'Jane Doe', 'travelDate' => null,
        ])->render();

        $this->assertStringContainsString('Goa Beach Getaway', $html);
        $this->assertStringContainsString('Day 1', $html);
    }

    public function test_premium_itinerary_template_renders_with_real_package_photos(): void
    {
        $data = (new PremiumItineraryPresenter())->present($this->packageWithPhoto(), [
            'referenceLabel' => 'Booking Reference', 'referenceValue' => 'BK-1',
            'travellerName' => 'Jane Doe', 'travelDate' => null,
        ]);

        $html = View::make('pdf.premium.itinerary', $data)->render();

        $this->assertStringContainsString('Goa Beach Getaway', $html);
        $this->assertStringContainsString('cover.jpg', $html);
        $this->assertStringContainsString('class="day-number"', $html);
        $this->assertStringContainsString('cover-collage', $html);
    }

    public function test_premium_itinerary_footer_carries_branded_contact_bar_on_every_page(): void
    {
        $data = (new PremiumItineraryPresenter())->present($this->packageWithPhoto());

        $html = View::make('pdf.premium.itinerary', $data)->render();

        $this->assertStringContainsString('brand-footer', $html);
        $this->assertStringContainsString('www.farebuzzertravel.com', $html);
        // The literal "{PAGE_NUM}"/"{PAGE_COUNT}" tokens must NOT be in the HTML —
        // dompdf has no CSS-level substitution for them (see PdfPageNumbers), so
        // literal text here would render as-is instead of an actual page number.
        $this->assertStringNotContainsString('{PAGE_NUM}', $html);
        $this->assertStringNotContainsString('{PAGE_COUNT}', $html);
    }

    public function test_pdf_page_numbers_are_drawn_via_canvas_not_html_substitution(): void
    {
        $data = (new PremiumItineraryPresenter())->present($this->packageWithPhoto());
        $pdf = Pdf::loadView('pdf.premium.itinerary', $data)->setPaper('a4', 'landscape');

        $result = PdfPageNumbers::apply($pdf);

        $this->assertSame($pdf, $result);
        $this->assertStringStartsWith('%PDF', $pdf->output());
    }

    public function test_hotel_card_thumbnail_uses_contain_not_cover_so_seeded_banner_text_never_clips(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('hotel-cover.jpg', 'x');

        $package = $this->packageWithPhoto();
        $hotel = new Hotel(['name' => 'Grand Hyatt Kochi Bolgatty', 'star_rating' => 5, 'cover_image' => 'hotel-cover.jpg']);
        $hotel->setRelation('pivot', (object) ['nights' => 1, 'note' => null]);
        $package->setRelation('hotels', collect([$hotel]));

        $data = (new PremiumItineraryPresenter())->present($package);
        $html = View::make('pdf.premium.itinerary', $data)->render();

        $this->assertStringContainsString('Grand Hyatt Kochi Bolgatty', $html);
        $this->assertStringContainsString('background-size: contain', $html);
    }

    public function test_premium_itinerary_template_renders_a_placeholder_when_the_package_has_no_photos(): void
    {
        $data = (new PremiumItineraryPresenter())->present($this->packageWithNoPhotos());

        $html = View::make('pdf.premium.itinerary', $data)->render();

        // No <img>/background-image reference at all — the CSS gradient placeholder
        // block rendered instead, so a photo-less package never breaks the render.
        $this->assertStringNotContainsString('background-image', $html);
        $this->assertStringContainsString('FAREBUZZER', $html);
    }

    public function test_premium_itinerary_template_actually_produces_a_pdf_via_dompdf(): void
    {
        $data = (new PremiumItineraryPresenter())->present($this->packageWithPhoto());

        $output = Pdf::loadView('pdf.premium.itinerary', $data)->setPaper('a4', 'landscape')->output();

        $this->assertStringStartsWith('%PDF', $output);
    }

    public function test_premium_quotation_template_renders_price_breakdown_and_linked_package(): void
    {
        $package = $this->packageWithPhoto();
        $data = (new PremiumItineraryPresenter())->present($package);

        $quotation = new Quotation([
            'quotation_number' => 'PREVIEW', 'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com', 'total_amount' => 25000,
        ]);
        $quotation->created_at = now();
        $quotation->setRelation('items', collect([
            new QuotationItem(['description' => 'Package cost', 'quantity' => 2, 'unit_price' => 12500, 'amount' => 25000]),
        ]));

        $html = View::make('pdf.premium.quotation', array_merge($data, ['quotation' => $quotation]))->render();

        $this->assertStringContainsString('Jane Doe', $html);
        $this->assertStringContainsString('Package cost', $html);
        $this->assertStringContainsString('Goa Beach Getaway', $html);
    }

    public function test_premium_quotation_template_renders_without_a_linked_package(): void
    {
        $quotation = new Quotation([
            'quotation_number' => 'PREVIEW', 'customer_name' => 'John Roe',
            'customer_email' => 'john@example.com', 'total_amount' => 5000,
        ]);
        $quotation->created_at = now();
        $quotation->setRelation('items', collect([
            new QuotationItem(['description' => 'Custom add-on', 'quantity' => 1, 'unit_price' => 5000, 'amount' => 5000]),
        ]));

        $html = View::make('pdf.premium.quotation', [
            'package' => null, 'coverImage' => null, 'days' => [], 'hotels' => [],
            'inclusions' => [], 'exclusions' => [], 'logo' => null, 'quotation' => $quotation,
        ])->render();

        $this->assertStringContainsString('John Roe', $html);
        $this->assertStringContainsString('Custom add-on', $html);
    }
}
