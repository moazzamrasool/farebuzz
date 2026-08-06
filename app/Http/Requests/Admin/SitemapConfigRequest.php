<?php

namespace App\Http\Requests\Admin;

use App\Models\SeoSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SitemapConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $types = array_keys(SeoSetting::DEFAULT_SITEMAP_CONFIG);

        return [
            'included' => 'nullable|array',
            'included.*' => Rule::in($types),
            'priority' => 'required|array',
            'priority.*' => 'required|numeric|min:0|max:1',
            'changefreq' => 'required|array',
            'changefreq.*' => 'required|in:always,hourly,daily,weekly,monthly,yearly,never',
        ];
    }
}
