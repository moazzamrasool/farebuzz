<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminRequest extends FormRequest
{
    // Gates a genuinely privileged operation (creating/editing a company owner) —
    // deviates from this app's usual blanket `true`, intentionally.
    public function authorize(): bool
    {
        return $this->user('admin')?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        $admin = $this->route('admin');

        return [
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('admins', 'email')->ignore($admin?->id)],
            'password' => $admin ? 'nullable|string|min:6' : 'required|string|min:6',
        ];
    }
}
