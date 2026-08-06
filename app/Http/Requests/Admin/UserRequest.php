<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $tenantId = $this->user('admin')->unique_id;

        return [
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('admins', 'email')->ignore($user?->id)],
            'password' => $user ? 'nullable|string|min:6' : 'required|string|min:6',
            'role_id'  => [
                'required',
                Rule::exists('roles', 'id')->where('unique_id', $tenantId),
            ],
        ];
    }
}
