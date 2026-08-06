<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PackageFeatureRequest;
use App\Models\PackageFeature;

// Shared CRUD for Inclusions & Exclusions — one table (package_features), split by
// the "type" column. InclusionController/ExclusionController just bind $type/$routeName.
abstract class PackageFeatureController extends Controller
{
    protected string $type;
    protected string $routeName;
    protected string $label;

    public function index()
    {
        $features = PackageFeature::where('type', $this->type)->orderBy('sort_order')->latest()->paginate(15);
        return view('admin.package-features.index', [
            'features'  => $features,
            'routeName' => $this->routeName,
            'label'     => $this->label,
        ]);
    }

    public function store(PackageFeatureRequest $request)
    {
        $data = $request->validated();
        $data['type'] = $this->type;
        $feature = PackageFeature::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$this->label} created successfully.",
                'feature' => $this->toJsonRow($feature),
            ]);
        }

        return redirect()->route("crm.{$this->routeName}.index")
            ->with('success', "{$this->label} created successfully.");
    }

    public function edit(PackageFeature $feature)
    {
        $this->ensureType($feature);

        return response()->json([
            'success' => true,
            'feature' => [
                'id'         => $feature->id,
                'title'      => $feature->title,
                'icon'       => $feature->icon,
                'status'     => $feature->status,
                'sort_order' => $feature->sort_order,
                'update_url' => route("crm.{$this->routeName}.update", $feature->id),
            ],
        ]);
    }

    public function update(PackageFeatureRequest $request, PackageFeature $feature)
    {
        $this->ensureType($feature);

        $feature->update($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$this->label} updated successfully.",
                'feature' => $this->toJsonRow($feature),
            ]);
        }

        return redirect()->route("crm.{$this->routeName}.index")
            ->with('success', "{$this->label} updated successfully.");
    }

    public function destroy(PackageFeature $feature)
    {
        $this->ensureType($feature);

        if ($feature->holidayPackages()->exists()) {
            return redirect()->route("crm.{$this->routeName}.index")
                ->with('error', "Cannot delete this {$this->label} — it is still attached to one or more holiday packages.");
        }

        $feature->delete();

        return redirect()->route("crm.{$this->routeName}.index")
            ->with('success', "{$this->label} deleted successfully.");
    }

    public function toggleStatus(PackageFeature $feature)
    {
        $this->ensureType($feature);

        $feature->update([
            'status' => $feature->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->route("crm.{$this->routeName}.index")
            ->with('success', 'Status updated successfully.');
    }

    private function ensureType(PackageFeature $feature): void
    {
        abort_unless($feature->type === $this->type, 404);
    }

    private function toJsonRow(PackageFeature $feature): array
    {
        return [
            'id'           => $feature->id,
            'title'        => $feature->title,
            'icon'         => $feature->icon,
            'status'       => $feature->status,
            'status_label' => ucfirst($feature->status),
            'sort_order'   => $feature->sort_order,
            'edit_url'     => route("crm.{$this->routeName}.edit", $feature->id),
            'destroy_url'  => route("crm.{$this->routeName}.destroy", $feature->id),
            'toggle_url'   => route("crm.{$this->routeName}.toggle-status", $feature->id),
        ];
    }
}
