<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TrackingScriptRequest extends FormRequest
{
    // Company-owner-only — raw script injection runs on every frontend page, so a
    // sub-admin (tier=user) must never reach this even if granted every other
    // permission. Mirrors WhatsAppSettingController's tier check.
    public function authorize(): bool
    {
        return (bool) $this->user('admin')?->isAdmin();
    }

    public function rules(): array
    {
        return [
            'header_script' => 'nullable|string',
            'header_enabled' => 'sometimes|boolean',
            'body_script' => 'nullable|string',
            'body_enabled' => 'sometimes|boolean',
            'footer_script' => 'nullable|string',
            'footer_enabled' => 'sometimes|boolean',
        ];
    }
}
