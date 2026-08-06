<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HolidayPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Days must always equal Nights + 1 — the day-wise itinerary/hotel/activity day
        // pickers all key off this count. The admin form auto-fills Days from Nights via JS;
        // this enforces it server-side too.
        $nights = (int) $this->input('nights', 0);

        return [
            // Basic
            'destination_id'   => 'required|exists:destinations,id',
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255',
            'category_ids'     => 'nullable|array',
            'category_ids.*'   => 'exists:travel_categories,id',
            'nights'           => 'required|integer|min:0',
            'days'             => ['required', 'integer', 'min:1', function ($attribute, $value, $fail) use ($nights) {
                if ((int) $value !== $nights + 1) {
                    $fail('Days must equal Nights + 1 ('.($nights + 1).').');
                }
            }],
            'hotel_category'   => 'nullable|string|max:255',
            'meals'            => 'nullable|string|max:255',
            'language'         => 'nullable|string|max:255',
            'places_to_visit'  => 'nullable|string|max:255',
            'is_best_seller'   => 'nullable|boolean',
            'featured'         => 'nullable|boolean',
            'status'           => 'required|in:active,inactive',

            // Pricing
            'price'                 => 'required|numeric|min:0',
            'discounted_price'      => 'nullable|numeric|min:0|lt:price',
            'booking_type'          => 'required|in:enquiry_only,book_enquiry',
            'departure_cities'      => 'nullable|array',
            'departure_cities.*'    => 'string|max:255',
            'room_types'                    => 'required|array|min:1',
            'room_types.*.name'             => 'required|string|max:255',
            'room_types.*.price'            => 'required|numeric|min:0',
            'room_types.*.discounted_price' => 'nullable|numeric|min:0|lte:room_types.*.price',

            // Overview
            'overview'         => 'nullable|string',
            'activity_ids'     => 'nullable|array',
            'activity_ids.*'   => 'exists:activities,id',

            // SEO
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:255',
            'focus_keyword'    => 'nullable|string|max:255',
            'seo_content'      => 'nullable|string',

            // Inclusions / Exclusions
            'inclusion_feature_ids'   => 'nullable|array',
            'inclusion_feature_ids.*' => 'exists:package_features,id',
            'exclusion_feature_ids'   => 'nullable|array',
            'exclusion_feature_ids.*' => 'exists:package_features,id',
            'custom_inclusions'       => 'nullable|array',
            'custom_inclusions.*'     => 'string|max:255',
            'custom_exclusions'       => 'nullable|array',
            'custom_exclusions.*'     => 'string|max:255',

            // Hotels — entirely optional add-ons, never blocks saving. Rows are keyed by
            // hotel_id (e.g. hotels[5][price]), mirroring the Activities tab below.
            // day_number is the check-in day this hotel's stay starts on (blank = not
            // tied to a specific day, shown in the page's "General Add-ons" fallback).
            'hotels'                    => 'nullable|array',
            'hotels.*.room_type_id'     => 'nullable|integer|exists:hotel_room_types,id',
            'hotels.*.price'            => 'nullable|numeric|min:0',
            'hotels.*.is_optional'      => 'nullable|boolean',
            'hotels.*.nights'           => 'nullable|integer|min:1',
            'hotels.*.sort_order'       => 'nullable|integer|min:0',
            'hotels.*.note'             => 'nullable|string|max:255',
            'hotels.*.day_number'       => 'nullable|integer|min:1',

            // Optional paid Activities (add-ons) — entirely optional, never blocks saving.
            'activities'                    => 'nullable|array',
            'activities.*.price'            => 'nullable|numeric|min:0',
            'activities.*.is_optional'      => 'nullable|boolean',
            'activities.*.sort_order'       => 'nullable|integer|min:0',
            'activities.*.note'             => 'nullable|string|max:255',
            'activities.*.day_number'       => 'nullable|integer|min:1',

            // Itinerary — day-by-day content; day_number is the source of truth the
            // frontend day plan and the Hotels/Activities day pickers key off.
            'itineraries'                   => 'nullable|array',
            'itineraries.*.day_number'      => 'nullable|integer|min:1',
            'itineraries.*.title'           => 'nullable|string|max:255',
            'itineraries.*.route_summary'   => 'nullable|string|max:255',
            'itineraries.*.detail'          => 'nullable|string',
            'itineraries.*.bullet_points'   => 'nullable|string',
            'itineraries.*.meal_tags'       => 'nullable|array',
            'itineraries.*.meal_tags.*'     => 'string|in:breakfast,lunch,dinner',

            // Photos
            'photos'              => 'nullable|array',
            'photos.*.id'         => 'nullable|integer|exists:package_photos,id',
            'photos.*.file'       => 'nullable|image|max:4096',
            'photos.*.is_cover'   => 'nullable|boolean',

            // FAQs
            'faqs'                => 'nullable|array',
            'faqs.*.id'           => 'nullable|integer|exists:package_faqs,id',
            'faqs.*.question'     => 'required_with:faqs.*.answer|string|max:255',
            'faqs.*.answer'       => 'nullable|string',

            // Reviews
            'reviews'                     => 'nullable|array',
            'reviews.*.id'                => 'nullable|integer|exists:package_reviews,id',
            'reviews.*.reviewer_name'     => 'required_with:reviews.*.rating|string|max:255',
            'reviews.*.rating'            => 'required_with:reviews.*.reviewer_name|numeric|min:0|max:5',
            'reviews.*.hotels_rating'     => 'nullable|numeric|min:0|max:5',
            'reviews.*.sightseeing_rating' => 'nullable|numeric|min:0|max:5',
            'reviews.*.food_rating'       => 'nullable|numeric|min:0|max:5',
            'reviews.*.value_rating'      => 'nullable|numeric|min:0|max:5',
            'reviews.*.comment'           => 'nullable|string',
            'reviews.*.review_date'       => 'required_with:reviews.*.reviewer_name|date',
            'reviews.*.verified'          => 'nullable|boolean',

            // Related
            'related_ids'     => 'nullable|array',
            'related_ids.*'   => 'exists:holiday_packages,id',
        ];
    }
}
