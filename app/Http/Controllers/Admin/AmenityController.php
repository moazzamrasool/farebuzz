<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AmenityRequest;
use App\Models\Amenity;

class AmenityController extends Controller
{
    public function index()
    {
        $amenities = Amenity::orderBy('sort_order')->latest()->paginate(15);
        return view('admin.amenities.index', compact('amenities'));
    }

    public function store(AmenityRequest $request)
    {
        $amenity = Amenity::create($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Amenity created successfully.',
                'amenity' => $this->toJsonRow($amenity),
            ]);
        }

        return redirect()->route('crm.amenities.index')
            ->with('success', 'Amenity created successfully.');
    }

    public function edit(Amenity $amenity)
    {
        return response()->json([
            'success' => true,
            'amenity' => [
                'id'         => $amenity->id,
                'name'       => $amenity->name,
                'icon'       => $amenity->icon,
                'status'     => $amenity->status,
                'sort_order' => $amenity->sort_order,
                'update_url' => route('crm.amenities.update', $amenity->id),
            ],
        ]);
    }

    public function update(AmenityRequest $request, Amenity $amenity)
    {
        $amenity->update($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Amenity updated successfully.',
                'amenity' => $this->toJsonRow($amenity),
            ]);
        }

        return redirect()->route('crm.amenities.index')
            ->with('success', 'Amenity updated successfully.');
    }

    public function destroy(Amenity $amenity)
    {
        if ($amenity->hotels()->exists()) {
            return redirect()->route('crm.amenities.index')
                ->with('error', 'Cannot delete this amenity — it is still linked to one or more hotels.');
        }

        $amenity->delete();

        return redirect()->route('crm.amenities.index')
            ->with('success', 'Amenity deleted successfully.');
    }

    public function toggleStatus(Amenity $amenity)
    {
        $amenity->update([
            'status' => $amenity->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->route('crm.amenities.index')
            ->with('success', 'Status updated successfully.');
    }

    private function toJsonRow(Amenity $amenity): array
    {
        return [
            'id'           => $amenity->id,
            'name'         => $amenity->name,
            'icon'         => $amenity->icon,
            'status'       => $amenity->status,
            'status_label' => ucfirst($amenity->status),
            'sort_order'   => $amenity->sort_order,
            'edit_url'     => route('crm.amenities.edit', $amenity->id),
            'destroy_url'  => route('crm.amenities.destroy', $amenity->id),
            'toggle_url'   => route('crm.amenities.toggle-status', $amenity->id),
        ];
    }
}
