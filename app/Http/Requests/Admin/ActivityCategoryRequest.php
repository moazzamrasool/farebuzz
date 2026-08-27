<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $activityCategory = $this->route('activityCategory');
        $uniqueId = $activityCategory->unique_id ?? $this->user('admin')->unique_id;

        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('activity_categories', 'name')
                    ->where('unique_id', $uniqueId)
                    ->ignore($activityCategory?->id),
            ],
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:100',
            'status'      => 'required|in:active,inactive',
            'sort_order'  => 'nullable|integer|min:0',
        ];
    }
}
