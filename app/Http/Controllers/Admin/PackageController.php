<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PackageRequest;
use App\Models\Package;
use App\Models\TravelCategory;
use App\Traits\GeneratesUniqueSlug;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    use GeneratesUniqueSlug;

    public function index()
    {
        $packages = Package::with('travelCategory')->orderBy('sort_order')->latest()->paginate(15);
        $travelCategories = TravelCategory::where('status', 'active')->orderBy('name')->get();
        return view('admin.packages.index', compact('packages', 'travelCategories'));
    }

    public function create()
    {
        $travelCategories = TravelCategory::where('status', 'active')->orderBy('name')->get();
        return view('admin.packages.create', compact('travelCategories'));
    }

    public function store(PackageRequest $request)
    {
        $data = $this->prepareData($request);
        $data['slug'] = $this->generateUniqueSlug(Package::class, $request->filled('slug') ? $request->slug : $request->name);

        $package = Package::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Package created successfully.',
                'package' => $this->toJsonRow($package),
            ]);
        }

        return redirect()->route('crm.packages.index')
            ->with('success', 'Package created successfully.');
    }

    public function edit(Package $package)
    {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'package' => [
                    'id'                 => $package->id,
                    'travel_category_id' => $package->travel_category_id,
                    'name'               => $package->name,
                    'slug'               => $package->slug,
                    'type'               => $package->type,
                    'description'        => $package->description,
                    'meta'               => $package->meta,
                    'status'             => $package->status,
                    'sort_order'         => $package->sort_order,
                    'image_url'          => $package->image ? asset('storage/'.$package->image) : null,
                    'banner_image_url'   => $package->banner_image ? asset('storage/'.$package->banner_image) : null,
                    'update_url'         => route('crm.packages.update', $package->id),
                ],
            ]);
        }

        $travelCategories = TravelCategory::where('status', 'active')->orderBy('name')->get();
        return view('admin.packages.edit', compact('package', 'travelCategories'));
    }

    public function update(PackageRequest $request, Package $package)
    {
        $data = $this->prepareData($request, $package);

        if ($request->filled('slug')) {
            if ($package->slug !== $request->slug) {
                $data['slug'] = $this->generateUniqueSlug(Package::class, $request->slug, $package->id);
            }
        } elseif ($package->name !== $request->name) {
            $data['slug'] = $this->generateUniqueSlug(Package::class, $request->name, $package->id);
        }

        $package->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Package updated successfully.',
                'package' => $this->toJsonRow($package),
            ]);
        }

        return redirect()->route('crm.packages.index')
            ->with('success', 'Package updated successfully.');
    }

    public function destroy(Package $package)
    {
        $this->deleteImages($package);
        $package->delete();

        return redirect()->route('crm.packages.index')
            ->with('success', 'Package deleted successfully.');
    }

    public function toggleStatus(Package $package)
    {
        $package->update([
            'status' => $package->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->route('crm.packages.index')
            ->with('success', 'Status updated successfully.');
    }

    // Shared JSON shape used to render/update a table row after store/update
    private function toJsonRow(Package $package): array
    {
        $package->loadMissing('travelCategory');

        return [
            'id'             => $package->id,
            'image_url'      => $package->image ? asset('storage/'.$package->image) : null,
            'name'           => $package->name,
            'slug'           => $package->slug,
            'category_name'  => $package->travelCategory->name ?? 'N/A',
            'type'           => $package->type,
            'type_label'     => ucfirst($package->type),
            'meta'           => $package->meta,
            'status'         => $package->status,
            'status_label'   => ucfirst($package->status),
            'created_at'     => $package->created_at->format('d-m-Y'),
            'edit_url'       => route('crm.packages.edit', $package->id),
            'destroy_url'    => route('crm.packages.destroy', $package->id),
            'toggle_url'     => route('crm.packages.toggle-status', $package->id),
        ];
    }

    // Shared store/update field prep: validated data + file uploads
    private function prepareData(PackageRequest $request, ?Package $package = null): array
    {
        $data = $request->validated();
        unset($data['image'], $data['banner_image']);

        if ($request->hasFile('image')) {
            if ($package?->image) {
                Storage::disk('public')->delete($package->image);
            }
            $data['image'] = $request->file('image')->store('packages', 'public');
        }

        if ($request->hasFile('banner_image')) {
            if ($package?->banner_image) {
                Storage::disk('public')->delete($package->banner_image);
            }
            $data['banner_image'] = $request->file('banner_image')->store('packages/banners', 'public');
        }

        return $data;
    }

    private function deleteImages(Package $package): void
    {
        if ($package->image) {
            Storage::disk('public')->delete($package->image);
        }
        if ($package->banner_image) {
            Storage::disk('public')->delete($package->banner_image);
        }
    }
}
