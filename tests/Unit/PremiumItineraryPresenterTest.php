<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Destination;
use App\Models\HolidayPackage;
use App\Models\HolidayPackageCustomFeature;
use App\Models\Hotel;
use App\Models\PackageFeature;
use App\Models\PackageItinerary;
use App\Models\PackagePhoto;
use App\Services\PremiumItineraryPresenter;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

// PremiumItineraryPresenter is where the "dynamic, not hardcoded" requirement
// actually lives: per-day images are assigned round-robin from the package's own
// photos (PackageItinerary has no image column of its own), falling back to the
// destination cover, falling back to null (→ CSS placeholder in the template).
// Every relation is set in-memory via setRelation() so present()'s internal
// loadMissing() finds everything already loaded and never touches the database
// — this repo has no test-database/factory setup for these tenant-scoped models,
// so these are pure in-memory unit tests rather than DB-backed feature tests.
class PremiumItineraryPresenterTest extends TestCase
{
    private function photo(string $path, bool $isCover = false): PackagePhoto
    {
        return new PackagePhoto(['path' => $path, 'is_cover' => $isCover, 'sort_order' => 0]);
    }

    private function day(int $number): PackageItinerary
    {
        return new PackageItinerary([
            'day_number'    => $number,
            'title'         => "Day {$number} title",
            'route_summary' => "Route {$number}",
            'detail'        => "Detail {$number}",
            'bullet_points' => ["Point {$number}"],
            'meal_tags'     => ['breakfast'],
        ]);
    }

    private function basePackage(): HolidayPackage
    {
        $package = new HolidayPackage([
            'title' => 'Test Package', 'nights' => 2, 'days' => 3,
            'hotel_category' => '4 Star Hotels', 'meals' => 'Daily Breakfast',
        ]);
        $package->setRelation('inclusionFeatures', collect());
        $package->setRelation('exclusionFeatures', collect());
        $package->setRelation('customInclusions', collect());
        $package->setRelation('customExclusions', collect());
        $package->setRelation('hotels', collect());
        $package->setRelation('optionalActivities', collect());

        return $package;
    }

    public function test_per_day_images_round_robin_over_the_packages_own_photos(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('a.jpg', 'x');
        Storage::disk('public')->put('b.jpg', 'x');

        $package = $this->basePackage();
        $package->setRelation('itineraries', collect([$this->day(1), $this->day(2), $this->day(3)]));
        $package->setRelation('photos', collect([$this->photo('a.jpg'), $this->photo('b.jpg')]));
        $package->setRelation('destination', null);

        $days = (new PremiumItineraryPresenter())->present($package)['days'];

        $this->assertStringContainsString('a.jpg', str_replace('\\', '/', $days[0]['image']));
        $this->assertStringContainsString('b.jpg', str_replace('\\', '/', $days[1]['image']));
        $this->assertStringContainsString('a.jpg', str_replace('\\', '/', $days[2]['image'])); // wraps back to photo 0
    }

    public function test_day_image_falls_back_to_destination_cover_when_package_has_no_photos(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('dest-cover.jpg', 'x');

        $package = $this->basePackage();
        $package->setRelation('itineraries', collect([$this->day(1)]));
        $package->setRelation('photos', collect());
        $package->setRelation('destination', new Destination(['name' => 'Goa', 'cover_image' => 'dest-cover.jpg']));

        $days = (new PremiumItineraryPresenter())->present($package)['days'];

        $this->assertStringContainsString('dest-cover.jpg', str_replace('\\', '/', $days[0]['image']));
    }

    public function test_day_image_is_null_when_nothing_is_available_so_the_template_can_render_a_placeholder(): void
    {
        $package = $this->basePackage();
        $package->setRelation('itineraries', collect([$this->day(1)]));
        $package->setRelation('photos', collect());
        $package->setRelation('destination', null);

        $days = (new PremiumItineraryPresenter())->present($package)['days'];

        $this->assertNull($days[0]['image']);
    }

    public function test_cover_image_prefers_the_flagged_cover_photo_over_the_first_photo(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('first.jpg', 'x');
        Storage::disk('public')->put('cover.jpg', 'x');

        $package = $this->basePackage();
        $package->setRelation('itineraries', collect());
        $package->setRelation('photos', collect([
            $this->photo('first.jpg'),
            $this->photo('cover.jpg', isCover: true),
        ]));
        $package->setRelation('destination', null);

        $result = (new PremiumItineraryPresenter())->present($package);

        $this->assertStringContainsString('cover.jpg', str_replace('\\', '/', $result['coverImage']));
    }

    public function test_inclusions_and_exclusions_merge_catalog_and_custom_features(): void
    {
        $package = $this->basePackage();
        $package->setRelation('itineraries', collect());
        $package->setRelation('photos', collect());
        $package->setRelation('destination', null);
        $package->setRelation('inclusionFeatures', collect([new PackageFeature(['title' => 'Airport Transfers'])]));
        $package->setRelation('customInclusions', collect([new HolidayPackageCustomFeature(['title' => 'Welcome Kit'])]));
        $package->setRelation('exclusionFeatures', collect([new PackageFeature(['title' => 'Airfare'])]));
        $package->setRelation('customExclusions', collect([new HolidayPackageCustomFeature(['title' => 'GST'])]));

        $result = (new PremiumItineraryPresenter())->present($package);

        $this->assertEquals(['Airport Transfers', 'Welcome Kit'], $result['inclusions']);
        $this->assertEquals(['Airfare', 'GST'], $result['exclusions']);
    }

    public function test_hotel_cards_carry_pivot_nights_and_note(): void
    {
        $package = $this->basePackage();
        $package->setRelation('itineraries', collect());
        $package->setRelation('photos', collect());
        $package->setRelation('destination', null);

        $hotel = new Hotel(['name' => 'Beach Resort', 'star_rating' => 4]);
        $hotel->setRelation('pivot', (object) ['nights' => 2, 'note' => 'Sea view room']);
        $package->setRelation('hotels', collect([$hotel]));

        $hotels = (new PremiumItineraryPresenter())->present($package)['hotels'];

        $this->assertSame('Beach Resort', $hotels[0]['name']);
        $this->assertSame(4, $hotels[0]['star_rating']);
        $this->assertSame(2, $hotels[0]['nights']);
        $this->assertSame('Sea view room', $hotels[0]['note']);
    }

    public function test_cover_collage_returns_up_to_three_tiles_cover_photo_first(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('first.jpg', 'x');
        Storage::disk('public')->put('cover.jpg', 'x');
        Storage::disk('public')->put('third.jpg', 'x');

        $package = $this->basePackage();
        $package->setRelation('itineraries', collect());
        $package->setRelation('photos', collect([
            $this->photo('first.jpg'),
            $this->photo('cover.jpg', isCover: true),
            $this->photo('third.jpg'),
        ]));
        $package->setRelation('destination', null);

        $result = (new PremiumItineraryPresenter())->present($package);

        $this->assertCount(3, $result['coverImages']);
        $this->assertStringContainsString('cover.jpg', str_replace('\\', '/', $result['coverImages'][0]));
        $this->assertSame($result['coverImages'][0], $result['coverImage']);
    }

    public function test_cover_collage_pads_missing_tiles_with_null_when_fewer_than_three_photos(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('only.jpg', 'x');

        $package = $this->basePackage();
        $package->setRelation('itineraries', collect());
        $package->setRelation('photos', collect([$this->photo('only.jpg')]));
        $package->setRelation('destination', null);

        $result = (new PremiumItineraryPresenter())->present($package);

        $this->assertCount(3, $result['coverImages']);
        $this->assertNotNull($result['coverImages'][0]);
        $this->assertNull($result['coverImages'][1]);
        $this->assertNull($result['coverImages'][2]);
    }

    public function test_day_supporting_images_prefer_the_activity_and_hotel_linked_to_that_day(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('activity.jpg', 'x');
        Storage::disk('public')->put('hotel.jpg', 'x');
        Storage::disk('public')->put('hero.jpg', 'x');

        $activity = new Activity(['name' => 'Boat Ride', 'image' => 'activity.jpg']);
        $activity->setRelation('pivot', (object) ['day_number' => 1]);

        $hotel = new Hotel(['name' => 'Lake Resort', 'cover_image' => 'hotel.jpg']);
        $hotel->setRelation('pivot', (object) ['day_number' => 1, 'nights' => 1, 'note' => null]);

        $package = $this->basePackage();
        $package->setRelation('itineraries', collect([$this->day(1)]));
        $package->setRelation('photos', collect([$this->photo('hero.jpg')]));
        $package->setRelation('destination', null);
        $package->setRelation('hotels', collect([$hotel]));
        $package->setRelation('optionalActivities', collect([$activity]));

        $days = (new PremiumItineraryPresenter())->present($package)['days'];
        $supporting = array_map(fn ($p) => str_replace('\\', '/', $p), $days[0]['supporting_images']);

        $this->assertCount(2, $supporting);
        $this->assertTrue(collect($supporting)->contains(fn ($p) => str_contains($p, 'activity.jpg')));
        $this->assertTrue(collect($supporting)->contains(fn ($p) => str_contains($p, 'hotel.jpg')));
    }

    public function test_day_supporting_images_backfill_from_other_photos_when_nothing_is_linked(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('a.jpg', 'x');
        Storage::disk('public')->put('b.jpg', 'x');
        Storage::disk('public')->put('c.jpg', 'x');

        $package = $this->basePackage();
        $package->setRelation('itineraries', collect([$this->day(1)]));
        $package->setRelation('photos', collect([$this->photo('a.jpg'), $this->photo('b.jpg'), $this->photo('c.jpg')]));
        $package->setRelation('destination', null);

        $days = (new PremiumItineraryPresenter())->present($package)['days'];

        // Day 1's hero is a.jpg (index 0) — supporting images backfill from the
        // remaining photos, never repeating the hero.
        $this->assertCount(2, $days[0]['supporting_images']);
        $this->assertNotContains($days[0]['image'], $days[0]['supporting_images']);
    }

    public function test_day_supporting_images_is_empty_when_only_one_photo_exists_for_the_whole_package(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('only.jpg', 'x');

        $package = $this->basePackage();
        $package->setRelation('itineraries', collect([$this->day(1)]));
        $package->setRelation('photos', collect([$this->photo('only.jpg')]));
        $package->setRelation('destination', null);

        $days = (new PremiumItineraryPresenter())->present($package)['days'];

        $this->assertSame([], $days[0]['supporting_images']);
    }
}
