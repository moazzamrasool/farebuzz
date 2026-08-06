<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BlockCompanyRequest extends FormRequest
{
    // Defense in depth alongside the 'admin.super' route middleware — block/unblock
    // must never be reachable by an Admin or User tier account.
    public function authorize(): bool
    {
        return $this->user('admin')?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'reason' => 'nullable|string|max:1000',
        ];
    }
}
