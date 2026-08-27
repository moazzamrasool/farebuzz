<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ActivityCategoryRequest;
use App\Models\ActivityCategory;
use App\Traits\GeneratesUniqueSlug;

class ActivityCategoryController extends Controller
{
    use GeneratesUniqueSlug;

    public function index()
    {
        $activityCategories = ActivityCategory::orderBy('sort_order')->latest()->paginate(15);

        return view('admin.activity-categories.index', compact('activityCategories'));
    }

    public function create()
    {
        return view('admin.activity-categories.create');
    }

    public function store(ActivityCategoryRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $this->generateUniqueSlug(ActivityCategory::class, $request->name);

        $category = ActivityCategory::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Activity category created successfully.',
                'category' => $this->toJsonRow($category),
            ]);
        }

        return redirect()->route('crm.activity-categories.index')
            ->with('success', 'Activity category created successfully.');
    }

    public function edit(ActivityCategory $activityCategory)
    {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success'  => true,
                'category' => [
                    'id'          => $activityCategory->id,
                    'name'        => $activityCategory->name,
                    'description' => $activityCategory->description,
                    'icon'        => $activityCategory->icon,
                    'status'      => $activityCategory->status,
                    'sort_order'  => $activityCategory->sort_order,
                    'update_url'  => route('crm.activity-categories.update', $activityCategory->id),
                ],
            ]);
        }

        return view('admin.activity-categories.edit', compact('activityCategory'));
    }

    public function update(ActivityCategoryRequest $request, ActivityCategory $activityCategory)
    {
        $data = $request->validated();

        if ($activityCategory->name !== $request->name) {
            $data['slug'] = $this->generateUniqueSlug(ActivityCategory::class, $request->name, $activityCategory->id);
        }

        $activityCategory->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Activity category updated successfully.',
                'category' => $this->toJsonRow($activityCategory),
            ]);
        }

        return redirect()->route('crm.activity-categories.index')
            ->with('success', 'Activity category updated successfully.');
    }

    public function destroy(ActivityCategory $activityCategory)
    {
        if ($activityCategory->activities()->exists()) {
            return redirect()->route('crm.activity-categories.index')
                ->with('error', 'Cannot delete this category — it still has activities linked to it.');
        }

        $activityCategory->delete();

        return redirect()->route('crm.activity-categories.index')
            ->with('success', 'Activity category deleted successfully.');
    }

    public function toggleStatus(ActivityCategory $activityCategory)
    {
        $activityCategory->update([
            'status' => $activityCategory->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->route('crm.activity-categories.index')
            ->with('success', 'Status updated successfully.');
    }

    private function toJsonRow(ActivityCategory $category): array
    {
        return [
            'id'           => $category->id,
            'name'         => $category->name,
            'slug'         => $category->slug,
            'icon'         => $category->icon,
            'sort_order'   => $category->sort_order,
            'status'       => $category->status,
            'status_label' => ucfirst($category->status),
            'edit_url'     => route('crm.activity-categories.edit', $category->id),
            'destroy_url'  => route('crm.activity-categories.destroy', $category->id),
            'toggle_url'   => route('crm.activity-categories.toggle-status', $category->id),
        ];
    }
}
