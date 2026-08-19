<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DestinationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $destinationId = $this->route('destination')?->id;

        return [
            'name'                => 'required|string|max:255',
            'slug'                => 'nullable|string|max:255|unique:destinations,slug,'.($destinationId ?? 'NULL').',id',
            'local'               => 'required|in:domestic,international',
            'country'             => 'required|string|max:255',
            'city'                => 'nullable|string|max:255',
            'description'         => 'nullable|string',
            'meta'                => 'nullable|string|max:1000',
            'meta_title'          => 'nullable|string|max:255',
            'meta_description'    => 'nullable|string|max:500',
            'packages_meta_title'       => 'nullable|string|max:255',
            'packages_meta_description' => 'nullable|string|max:500',
            'meta_keywords'       => 'nullable|string|max:255',
            'focus_keyword'       => 'nullable|string|max:255',
            'seo_content'         => 'nullable|string',
            'tags'                => 'nullable|string|max:255',
            'canonical_url'       => 'nullable|url|max:255',
            'og_title'            => 'nullable|string|max:255',
            'og_description'      => 'nullable|string|max:500',
            'og_image'            => 'nullable|image|max:2048',
            'robots_index'        => 'nullable|boolean',
            'robots_follow'       => 'nullable|boolean',
            'cover_image'         => 'nullable|image|max:2048',
            'gallery_images.*'    => 'nullable|image|max:2048',
            'status'              => 'required|in:active,inactive',
            'sort_order'          => 'nullable|integer|min:0',
            'featured'            => 'nullable|boolean',
        ];
    }
}
