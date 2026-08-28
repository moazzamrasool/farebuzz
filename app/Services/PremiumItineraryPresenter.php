<?php

namespace App\Services;

use App\Models\HolidayPackage;
use App\Support\PdfImage;
use Illuminate\Support\Collection;

// Builds the view-model the premium PDF templates (resources/views/pdf/premium/*)
// render from — every image slot resolved up front via PdfImage::resolve() so the
// templates only ever deal with "a local path, or null" and never need their own
// missing-image handling. When a day has its own uploaded images (admin's "Day
// Images" repeater), those are used directly — first as the hero, up to 2 more as
// supporting tiles — capped at 3 total to fit the existing fixed-height A4 layout
// and avoid bloating the PDF with every uploaded image. Days with no images of
// their own fall back to the original scheme: hero assigned round-robin from the
// package's own photos() pool, supporting images preferring whatever
// activity/hotel is linked to that day_number, backfilled from the photo pool.
class PremiumItineraryPresenter
{
    public function present(HolidayPackage $package, array $meta = []): array
    {
        $package->loadMissing([
            'itineraries.images', 'photos', 'destination', 'hotels', 'optionalActivities',
            'inclusionFeatures', 'exclusionFeatures', 'customInclusions', 'customExclusions',
        ]);

        $photos = $package->photos;
        $destinationCover = $package->destination?->cover_image;

        $coverImages = $this->coverImages($photos, $destinationCover);

        $activitiesByDay = $package->optionalActivities->groupBy('pivot.day_number');
        $hotelsByDay = $package->hotels->groupBy('pivot.day_number');

        $days = $package->itineraries->values()->map(function ($day, $index) use ($photos, $destinationCover, $activitiesByDay, $hotelsByDay) {
            $ownImages = $day->images->map(fn ($img) => PdfImage::resolve($img->image))->filter()->values();

            if ($ownImages->isNotEmpty()) {
                $heroImage = $ownImages->first();
                $supportingImages = $ownImages->slice(1, 2)->values()->all();
            } else {
                $photo = $photos->isNotEmpty() ? $photos[$index % $photos->count()] : null;
                $heroImage = PdfImage::resolve($photo?->path ?? $destinationCover);
                $supportingImages = $this->supportingImagesForDay(
                    $day->day_number, $index, $heroImage, $photos,
                    $activitiesByDay->get($day->day_number, collect()),
                    $hotelsByDay->get($day->day_number, collect())
                );
            }

            return [
                'day_number'         => $day->day_number,
                'title'              => $day->title,
                'route_summary'      => $day->route_summary,
                'detail'             => $day->detail,
                'bullet_points'      => $day->bullet_points ?? [],
                'meal_tags'          => $day->meal_tags ?? [],
                'image'              => $heroImage,
                'supporting_images'  => $supportingImages,
            ];
        })->all();

        $hotels = $package->hotels->map(fn ($hotel) => [
            'name'        => $hotel->name,
            'star_rating' => $hotel->star_rating,
            'image'       => PdfImage::resolve($hotel->cover_image),
            'nights'      => $hotel->pivot->nights,
            'note'        => $hotel->pivot->note,
        ])->all();

        $inclusions = $package->inclusionFeatures->pluck('title')
            ->merge($package->customInclusions->pluck('title'))
            ->filter()->values()->all();

        $exclusions = $package->exclusionFeatures->pluck('title')
            ->merge($package->customExclusions->pluck('title'))
            ->filter()->values()->all();

        return [
            'package'        => $package,
            'coverImage'     => $coverImages[0] ?? null,
            'coverImages'    => $coverImages,
            'days'           => $days,
            'hotels'         => $hotels,
            'inclusions'     => $inclusions,
            'exclusions'     => $exclusions,
            'logo'           => PdfImage::resolve('frontend/img/logo.png'),
            'referenceLabel' => $meta['referenceLabel'] ?? null,
            'referenceValue' => $meta['referenceValue'] ?? null,
            'travellerName'  => $meta['travellerName'] ?? null,
            'travelDate'     => $meta['travelDate'] ?? null,
        ];
    }

    // Up to 3 tiles for the cover collage: the flagged cover photo first (if any),
    // then the rest of the package's own photos in order, falling back to the
    // destination cover for the first tile when the package has no photos at all.
    // Unfilled tiles are null — the template renders those as the branded placeholder.
    private function coverImages(Collection $photos, ?string $destinationCover): array
    {
        $ordered = $photos->sortByDesc('is_cover')->values();

        return collect(range(0, 2))->map(function ($i) use ($ordered, $destinationCover) {
            if ($ordered->has($i)) {
                return PdfImage::resolve($ordered[$i]->path);
            }

            return $i === 0 ? PdfImage::resolve($destinationCover) : null;
        })->all();
    }

    // Up to 2 supporting images for a day: prefer photos of activities/hotels
    // actually linked to this day_number, backfilled from the package's other
    // photos (skipping the one already used as this day's hero) when nothing
    // is linked or not enough is. Never errors on a package with too few
    // photos to backfill — it just returns fewer than 2.
    private function supportingImagesForDay(
        int $dayNumber, int $index, ?string $heroImage, Collection $photos,
        Collection $dayActivities, Collection $dayHotels
    ): array {
        $linked = $dayActivities->pluck('image')
            ->concat($dayHotels->pluck('cover_image'))
            ->filter()
            ->map(fn ($path) => PdfImage::resolve($path))
            ->filter()
            ->unique()
            ->values();

        $supporting = $linked->take(2);

        $needed = 2 - $supporting->count();
        if ($needed > 0 && $photos->count() > 1) {
            for ($offset = 1; $offset < $photos->count() && $supporting->count() < 2; $offset++) {
                $candidate = PdfImage::resolve($photos[($index + $offset) % $photos->count()]->path);

                if ($candidate && $candidate !== $heroImage && !$supporting->contains($candidate)) {
                    $supporting->push($candidate);
                }
            }
        }

        return $supporting->values()->all();
    }
}
