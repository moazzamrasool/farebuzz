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
            'destination_id'     => 'nullable|exists:destinations,id',
            'travel_category_id' => 'nullable|exists:travel_categories,id',
            'name'            => 'required|string|max:255',
            'category'        => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'duration'        => 'nullable|string|max:255',
            'price'           => 'nullable|numeric|min:0',
            'image'           => 'nullable|image|max:2048',
            'status'          => 'required|in:active,inactive',
            'sort_order'      => 'nullable|integer|min:0',
        ];
    }
}
