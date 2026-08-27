<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destination_id'        => 'nullable|exists:destinations,id',
            'travel_category_id'    => 'nullable|exists:travel_categories,id',
            'activity_category_id'  => 'nullable|exists:activity_categories,id',
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'duration'        => 'nullable|string|max:255',
            'price'           => 'nullable|numeric|min:0',
            'image'           => 'nullable|image|max:2048',
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
        ];
    }
}
