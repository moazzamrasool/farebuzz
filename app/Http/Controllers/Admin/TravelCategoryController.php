<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TravelCategoryRequest;
use App\Models\TravelCategory;
use App\Traits\GeneratesUniqueSlug;
use Illuminate\Support\Facades\Storage;

class TravelCategoryController extends Controller
{
    use GeneratesUniqueSlug;

    public function index()
    {
        $travelCategories = TravelCategory::orderBy('sort_order')->latest()->paginate(15);
        return view('admin.travel-categories.index', compact('travelCategories'));
    }

    public function create()
    {
        return view('admin.travel-categories.create');
    }

    public function store(TravelCategoryRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $this->generateUniqueSlug(TravelCategory::class, $request->name);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('travel-categories', 'public');
        }

        $category = TravelCategory::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Travel category created successfully.',
                'category' => [
                    'id'          => $category->id,
                    'image_url'   => $category->image ? asset('storage/'.$category->image) : null,
                    'name'        => $category->name,
                    'slug'        => $category->slug,
                    'badge_color' => $category->badge_color,
                    'sort_order'  => $category->sort_order,
                    'status'      => $category->status,
                    'status_label'=> ucfirst($category->status),
                    'edit_url'    => route('crm.travel-categories.edit', $category->id),
                    'destroy_url' => route('crm.travel-categories.destroy', $category->id),
                    'toggle_url'  => route('crm.travel-categories.toggle-status', $category->id),
                ],
            ]);
        }

        return redirect()->route('crm.travel-categories.index')
            ->with('success', 'Travel category created successfully.');
    }

    public function edit(TravelCategory $travelCategory)
    {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success'  => true,
                'category' => [
                    'id'          => $travelCategory->id,
                    'name'        => $travelCategory->name,
                    'description' => $travelCategory->description,
                    'badge_color' => $travelCategory->badge_color,
                    'status'      => $travelCategory->status,
                    'sort_order'  => $travelCategory->sort_order,
                    'image_url'   => $travelCategory->image ? asset('storage/'.$travelCategory->image) : null,
                    'update_url'  => route('crm.travel-categories.update', $travelCategory->id),
                ],
            ]);
        }

        return view('admin.travel-categories.edit', compact('travelCategory'));
    }

    public function update(TravelCategoryRequest $request, TravelCategory $travelCategory)
    {
        $data = $request->validated();

        if ($travelCategory->name !== $request->name) {
            $data['slug'] = $this->generateUniqueSlug(TravelCategory::class, $request->name, $travelCategory->id);
        }

        if ($request->hasFile('image')) {
            if ($travelCategory->image) {
                Storage::disk('public')->delete($travelCategory->image);
            }
            $data['image'] = $request->file('image')->store('travel-categories', 'public');
        }

        $travelCategory->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Travel category updated successfully.',
                'category' => [
                    'id'           => $travelCategory->id,
                    'image_url'    => $travelCategory->image ? asset('storage/'.$travelCategory->image) : null,
                    'name'         => $travelCategory->name,
                    'slug'         => $travelCategory->slug,
                    'sort_order'   => $travelCategory->sort_order,
                    'status'       => $travelCategory->status,
                    'status_label' => ucfirst($travelCategory->status),
                ],
            ]);
        }

        return redirect()->route('crm.travel-categories.index')
            ->with('success', 'Travel category updated successfully.');
    }

    public function destroy(TravelCategory $travelCategory)
    {
        if ($travelCategory->packages()->exists() || $travelCategory->holidayPackages()->exists()) {
            return redirect()->route('crm.travel-categories.index')
                ->with('error', 'Cannot delete this category — it still has packages or holiday packages linked to it.');
        }

        if ($travelCategory->image) {
            Storage::disk('public')->delete($travelCategory->image);
        }

        $travelCategory->delete();

        return redirect()->route('crm.travel-categories.index')
            ->with('success', 'Travel category deleted successfully.');
    }

    public function toggleStatus(TravelCategory $travelCategory)
    {
        $travelCategory->update([
            'status' => $travelCategory->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->route('crm.travel-categories.index')
            ->with('success', 'Status updated successfully.');
    }
}
