<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $coupon = $this->route('coupon');
        $uniqueId = $coupon->unique_id ?? $this->user('admin')->unique_id;

        return [
            'code' => [
                'required', 'string', 'max:50', 'alpha_dash',
                Rule::unique('coupons', 'code')
                    ->where('unique_id', $uniqueId)
                    ->ignore($coupon?->id),
            ],
            'title'                => 'required|string|max:255',
            'description'          => 'nullable|string|max:1000',
            'discount_type'        => 'required|in:percentage,fixed',
            'discount_value'       => 'required|numeric|min:0.01',
            'max_discount_amount'  => 'nullable|numeric|min:0',
            'min_booking_amount'   => 'nullable|numeric|min:0',
            'valid_from'           => 'required|date',
            'valid_to'             => 'required|date|after_or_equal:valid_from',
            'usage_limit'          => 'nullable|integer|min:1',
            'per_user_limit'       => 'nullable|integer|min:1',
            'applicable_to'        => 'required|in:all,packages,hotels,specific_packages,specific_hotels',
            'applicable_ids'       => 'nullable|array',
            'applicable_ids.*'     => 'integer',
            'banner_image'         => 'nullable|image|max:2048',
            'banner_link'          => 'nullable|string|max:255',
            'status'               => 'required|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'code.alpha_dash' => 'The code may only contain letters, numbers, dashes and underscores.',
        ];
    }
}
