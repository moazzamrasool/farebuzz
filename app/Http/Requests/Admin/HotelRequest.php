<?php

namespace App\Http\Requests\Admin;

use App\Enums\BedType;
use Illuminate\Foundation\Http\FormRequest;

class HotelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:255',
            'slug'            => 'nullable|string|max:255',
            'destination_id'  => 'nullable|exists:destinations,id',
            'star_rating'     => 'nullable|integer|min:0|max:5',
            'address'         => 'nullable|string|max:255',
            'latitude'        => 'nullable|numeric|between:-90,90',
            'longitude'       => 'nullable|numeric|between:-180,180',
            'check_in_time'   => 'nullable|string|max:10',
            'check_out_time'  => 'nullable|string|max:10',
            'property_rules'  => 'nullable|string',
            'description'     => 'nullable|string',
            'rating_score'    => 'nullable|numeric|min:0|max:10',
            'review_count'    => 'nullable|integer|min:0',
            'cover_image'     => 'nullable|image|max:2048',
            'gallery_images'   => 'nullable|array',
            'gallery_images.*' => 'image|max:2048',
            'amenity_ids'     => 'nullable|array',
            'amenity_ids.*'   => 'exists:amenities,id',
            'status'          => 'required|in:active,inactive',
            'sort_order'      => 'nullable|integer|min:0',

            // SEO
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:255',
            'focus_keyword'    => 'nullable|string|max:255',
            'tags'             => 'nullable|string|max:255',
            'canonical_url'    => 'nullable|url|max:255',
            'og_title'         => 'nullable|string|max:255',
            'og_description'   => 'nullable|string|max:500',
            'og_image'         => 'nullable|image|max:2048',
            'robots_index'     => 'nullable|boolean',
            'robots_follow'    => 'nullable|boolean',

            // Room Types — priced per night. Entirely optional; a hotel can be saved with none.
            'room_types'                        => 'nullable|array',
            'room_types.*.id'                   => 'nullable|integer|exists:hotel_room_types,id',
            'room_types.*.name'                 => 'required_with:room_types.*.price|string|max:255',
            'room_types.*.price'                => 'required_with:room_types.*.name|numeric|min:0',
            'room_types.*.discounted_price'     => 'nullable|numeric|min:0|lte:room_types.*.price',
            'room_types.*.occupancy_adults'     => 'nullable|integer|min:1|max:20',
            'room_types.*.occupancy_children'   => 'nullable|integer|min:0|max:20',
            'room_types.*.bed_type'             => ['nullable', 'in:'.implode(',', array_column(BedType::cases(), 'value'))],
            'room_types.*.size_sqft'            => 'nullable|integer|min:0',
            'room_types.*.meal_plan'            => 'nullable|in:room_only,breakfast,breakfast_dinner',
            'room_types.*.refundable'           => 'nullable|boolean',
            'room_types.*.images'               => 'nullable|array',
            'room_types.*.images.*'             => 'image|max:2048',
        ];
    }
}
