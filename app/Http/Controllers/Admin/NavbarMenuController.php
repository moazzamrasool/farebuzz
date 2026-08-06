<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NavbarMenuItemRequest;
use App\Models\CmsPage;
use App\Models\NavbarMenuItem;
use App\Models\TravelCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route as RouteFacade;

class NavbarMenuController extends Controller
{
    public function index()
    {
        $items = NavbarMenuItem::with('parent')->orderBy('sort_order')->get();

        return view('admin.navbar-menu.index', array_merge(['items' => $items], $this->formOptions()));
    }

    public function store(NavbarMenuItemRequest $request)
    {
        $data = $request->validated();
        $data['open_in_new_tab'] = $request->boolean('open_in_new_tab');

        $item = NavbarMenuItem::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Navbar item created successfully.',
                'item'    => $this->toJsonRow($item),
            ]);
        }

        return redirect()->route('crm.navbar-menu.index')->with('success', 'Navbar item created successfully.');
    }

    public function edit(NavbarMenuItem $navbarMenuItem)
    {
        return response()->json([
            'success' => true,
            'item'    => [
                'id'               => $navbarMenuItem->id,
                'parent_id'        => $navbarMenuItem->parent_id,
                'label'            => $navbarMenuItem->label,
                'link_type'        => $navbarMenuItem->link_type,
                'link_value'       => $navbarMenuItem->link_value,
                'open_in_new_tab'  => $navbarMenuItem->open_in_new_tab,
                'sort_order'       => $navbarMenuItem->sort_order,
                'status'           => $navbarMenuItem->status,
                'update_url'       => route('crm.navbar-menu.update', $navbarMenuItem),
            ],
        ]);
    }

    public function update(NavbarMenuItemRequest $request, NavbarMenuItem $navbarMenuItem)
    {
        $data = $request->validated();
        $data['open_in_new_tab'] = $request->boolean('open_in_new_tab');

        $navbarMenuItem->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Navbar item updated successfully.',
                'item'    => $this->toJsonRow($navbarMenuItem),
            ]);
        }

        return redirect()->route('crm.navbar-menu.index')->with('success', 'Navbar item updated successfully.');
    }

    public function destroy(NavbarMenuItem $navbarMenuItem)
    {
        $navbarMenuItem->delete();

        return redirect()->route('crm.navbar-menu.index')->with('success', 'Navbar item deleted successfully.');
    }

    public function toggleStatus(NavbarMenuItem $navbarMenuItem)
    {
        $navbarMenuItem->update([
            'status' => $navbarMenuItem->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->route('crm.navbar-menu.index')->with('success', 'Status updated successfully.');
    }

    // Mirrors HomepageSectionController::reorder() — a flat array of ids in their new
    // visual order, written straight to sort_order by position.
    public function reorder(Request $request)
    {
        foreach ($request->input('order', []) as $index => $id) {
            NavbarMenuItem::whereKey($id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    private function formOptions(): array
    {
        return [
            'parentOptions'   => NavbarMenuItem::topLevel()->orderBy('sort_order')->get(['id', 'label']),
            'routeOptions'    => collect(RouteFacade::getRoutes())
                ->filter(fn ($r) => $r->getName() && $r->methods()[0] === 'GET' && !str_starts_with($r->getName(), 'crm.') && !str_contains($r->uri(), '{'))
                ->pluck('action.as')
                ->unique()
                ->sort()
                ->values(),
            'cmsPageOptions'  => CmsPage::orderBy('title')->get(['id', 'title', 'slug']),
            'categoryOptions' => TravelCategory::orderBy('name')->get(['id', 'name', 'slug']),
        ];
    }

    private function toJsonRow(NavbarMenuItem $item): array
    {
        $item->loadMissing('parent');

        return [
            'id'             => $item->id,
            'label'          => $item->label,
            'link_type'      => $item->link_type,
            'link_value'     => $item->link_value,
            'parent_label'   => $item->parent->label ?? null,
            'open_in_new_tab'=> $item->open_in_new_tab,
            'status'         => $item->status,
            'status_label'   => ucfirst($item->status),
            'edit_url'       => route('crm.navbar-menu.edit', $item),
            'destroy_url'    => route('crm.navbar-menu.destroy', $item),
            'toggle_url'     => route('crm.navbar-menu.toggle-status', $item),
        ];
    }
}
