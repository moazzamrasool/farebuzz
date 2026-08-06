<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $packageId = $this->route('package')?->id;

        return [
            'travel_category_id' => 'required|exists:travel_categories,id',
            'name'                => 'required|string|max:255',
            'slug'                => 'nullable|string|max:255|unique:packages,slug,'.($packageId ?? 'NULL').',id',
            'type'                => 'required|in:domestic,international',
            'description'         => 'nullable|string',
            'meta'                => 'nullable|string|max:1000',
            'image'               => 'nullable|image|max:2048',
            'banner_image'        => 'nullable|image|max:4096',
            'status'              => 'required|in:active,inactive',
            'sort_order'          => 'nullable|integer|min:0',
        ];
    }
}
