<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NavbarMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $itemId = $this->route('navbar_menu_item')?->id;

        return [
            'label'           => 'required|string|max:255',
            'link_type'       => 'required|in:route,cms_page,category,custom',
            'link_value'      => 'required|string|max:255',
            // Only top-level items can be a parent — keeps the dropdown one level deep.
            // Also can't be its own parent when editing.
            'parent_id'       => array_filter([
                'nullable',
                Rule::exists('navbar_menu_items', 'id')->where('parent_id', null),
                $itemId ? Rule::notIn([$itemId]) : null,
            ]),
            'open_in_new_tab' => 'nullable|boolean',
            'sort_order'      => 'nullable|integer|min:0',
            'status'          => 'required|in:active,inactive',
        ];
    }
}
