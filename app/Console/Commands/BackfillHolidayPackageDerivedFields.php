<?php

namespace App\Console\Commands;

use App\Models\HolidayPackage;
use Illuminate\Console\Command;

class BackfillHolidayPackageDerivedFields extends Command
{
    protected $signature = 'holiday-packages:backfill-derived-fields {--apply : Actually write changes. Without this flag, only a preview is printed.}';

    protected $description = 'Recompute nights/days/hotel_category/meals for every holiday package from its itinerary and attached hotels';

    // Existing hotel_category text that already reads as a pure star-range (e.g. "3 to 5
    // Star Hotels") can be safely replaced by a freshly derived range. Anything else — most
    // notably a property-type annotation like "4 & 5 Star Hotels + Houseboat" — carries
    // information star ratings alone can't reproduce, so it's preserved and the Override
    // flag is pre-ticked instead of silently overwriting it.
    private const STAR_PATTERN = '/^\d+\s*(?:(?:&|to)\s*\d+\s*)?Star Hotels$/i';

    public function handle(): int
    {
        $apply = $this->option('apply');

        $packages = HolidayPackage::withoutGlobalScopes()
            ->with(['itineraries', 'hotels'])
            ->orderBy('id')
            ->get();

        if ($packages->isEmpty()) {
            $this->info('No holiday packages found. Nothing to do.');

            return self::SUCCESS;
        }

        $this->line($apply ? 'Applying backfill…' : 'DRY RUN — nothing will be written. Re-run with --apply to commit.');
        $this->newLine();

        $plan = [];
        foreach ($packages as $package) {
            $dayCount = $package->itineraries->count();
            $newNights = $dayCount > 0 ? $dayCount - 1 : 0;
            $newDays = $dayCount;
            $newMeals = $package->deriveMeals();

            $currentHotelCategory = $package->hotel_category;
            $derivedHotelCategory = $package->deriveHotelCategory();
            $looksLikeCustomAnnotation = $currentHotelCategory !== null
                && trim($currentHotelCategory) !== ''
                && !preg_match(self::STAR_PATTERN, trim($currentHotelCategory));

            $willOverrideHotelCategory = $package->hotel_category_overridden || $looksLikeCustomAnnotation;
            $newHotelCategory = $willOverrideHotelCategory ? $currentHotelCategory : $derivedHotelCategory;

            $plan[] = [
                'id' => $package->id,
                'title' => $package->title,
                'nights' => $package->nights.' → '.$newNights,
                'days' => $package->days.' → '.$newDays,
                'hotel_category' => ($currentHotelCategory ?? '(none)').
                    ($willOverrideHotelCategory
                        ? ' (kept, override pre-ticked)'
                        : ' → '.($newHotelCategory ?? '(none)')),
                'meals' => ($package->meals ?? '(none)').' → '.($newMeals ?? '(none)'),
                '_new' => [
                    'nights' => $newNights,
                    'days' => $newDays,
                    'hotel_category' => $newHotelCategory,
                    'hotel_category_overridden' => $willOverrideHotelCategory,
                    'meals' => $newMeals,
                ],
            ];
        }

        $this->table(
            ['ID', 'Title', 'Nights', 'Days', 'Hotel Category', 'Meals'],
            array_map(fn ($row) => [$row['id'], $row['title'], $row['nights'], $row['days'], $row['hotel_category'], $row['meals']], $plan)
        );

        if (!$apply) {
            return self::SUCCESS;
        }

        foreach ($plan as $row) {
            HolidayPackage::withoutGlobalScopes()->whereKey($row['id'])->update($row['_new']);
        }

        $this->newLine();
        $this->info('Backfill complete for '.count($plan).' package(s). meals_overridden was left untouched (all false) — no existing meals text carried an annotation worth preserving.');

        return self::SUCCESS;
    }
}
