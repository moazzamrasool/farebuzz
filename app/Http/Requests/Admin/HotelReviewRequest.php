<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HotelReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hotel_id'            => 'required|exists:hotels,id',
            'reviewer_name'       => 'required|string|max:255',
            'rating'              => 'required|numeric|min:0|max:10',
            'location_rating'     => 'nullable|numeric|min:0|max:10',
            'cleanliness_rating'  => 'nullable|numeric|min:0|max:10',
            'service_rating'      => 'nullable|numeric|min:0|max:10',
            'value_rating'        => 'nullable|numeric|min:0|max:10',
            'comment'             => 'nullable|string|max:2000',
            'review_date'         => 'required|date',
            'verified'            => 'nullable|boolean',
            'sort_order'          => 'nullable|integer|min:0',
        ];
    }
}
