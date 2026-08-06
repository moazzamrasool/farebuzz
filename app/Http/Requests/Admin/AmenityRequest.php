<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AmenityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => 'required|string|max:255',
            'icon'       => 'nullable|string|max:100',
            'status'     => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }
}
