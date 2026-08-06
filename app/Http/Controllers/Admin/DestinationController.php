<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DestinationsTemplateExport;
use App\Http\Controllers\Admin\Concerns\HandlesBulkImportResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DestinationRequest;
use App\Imports\DestinationsImport;
use App\Models\Destination;
use App\Traits\GeneratesUniqueSlug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class DestinationController extends Controller
{
    use GeneratesUniqueSlug, HandlesBulkImportResponse;

    public function index()
    {
        $destinations = Destination::orderBy('sort_order')->latest()->paginate(15);
        return view('admin.destinations.index', compact('destinations'));
    }

    public function create()
    {
        return view('admin.destinations.create');
    }

    public function store(DestinationRequest $request)
    {
        $data = $this->prepareData($request);
        $data['slug'] = $this->generateUniqueSlug(Destination::class, $request->filled('slug') ? $request->slug : $request->name);

        $destination = Destination::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Destination created successfully.',
                'destination' => $this->toJsonRow($destination),
            ]);
        }

        return redirect()->route('crm.destinations.index')
            ->with('success', 'Destination created successfully.');
    }

    public function edit(Destination $destination)
    {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'destination' => [
                    'id'                 => $destination->id,
                    'name'               => $destination->name,
                    'slug'               => $destination->slug,
                    'local'              => $destination->local,
                    'country'            => $destination->country,
                    'city'               => $destination->city,
                    'description'        => $destination->description,
                    'meta'               => $destination->meta,
                    'status'             => $destination->status,
                    'sort_order'         => $destination->sort_order,
                    'featured'           => $destination->featured,
                    'cover_image_url'    => $destination->cover_image ? asset('storage/'.$destination->cover_image) : null,
                    'gallery_image_urls' => collect($destination->gallery_images ?? [])->map(fn ($img) => asset('storage/'.$img))->values(),
                    'update_url'         => route('crm.destinations.update', $destination->id),
                ],
            ]);
        }

        return view('admin.destinations.edit', compact('destination'));
    }

    public function update(DestinationRequest $request, Destination $destination)
    {
        $data = $this->prepareData($request, $destination);

        if ($request->filled('slug')) {
            if ($destination->slug !== $request->slug) {
                $data['slug'] = $this->generateUniqueSlug(Destination::class, $request->slug, $destination->id);
            }
        } elseif ($destination->name !== $request->name) {
            $data['slug'] = $this->generateUniqueSlug(Destination::class, $request->name, $destination->id);
        }

        $destination->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Destination updated successfully.',
                'destination' => $this->toJsonRow($destination),
            ]);
        }

        return redirect()->route('crm.destinations.index')
            ->with('success', 'Destination updated successfully.');
    }

    public function destroy(Destination $destination)
    {
        if ($destination->holidayPackages()->exists()) {
            return redirect()->route('crm.destinations.index')
                ->with('error', 'Cannot delete this destination — it still has holiday packages linked to it.');
        }

        $this->deleteImages($destination);
        $destination->delete();

        return redirect()->route('crm.destinations.index')
            ->with('success', 'Destination deleted successfully.');
    }

    public function toggleStatus(Destination $destination)
    {
        $destination->update([
            'status' => $destination->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->route('crm.destinations.index')
            ->with('success', 'Status updated successfully.');
    }

    public function downloadTemplate()
    {
        return Excel::download(new DestinationsTemplateExport, 'destinations-template.xlsx');
    }

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $import = new DestinationsImport;
        Excel::import($import, $request->file('file'));

        return $this->bulkImportResponse($import->importedCount(), $import->rowErrors());
    }

    public function downloadErrorReport(string $token)
    {
        return $this->streamImportErrorReport($token);
    }

    protected function bulkUploadErrorRouteName(): string
    {
        return 'crm.destinations.bulk-upload.errors';
    }

    // Shared JSON shape used to render/update a table row after store/update
    private function toJsonRow(Destination $destination): array
    {
        return [
            'id'             => $destination->id,
            'cover_image_url'=> $destination->cover_image ? asset('storage/'.$destination->cover_image) : null,
            'name'           => $destination->name,
            'slug'           => $destination->slug,
            'local'          => $destination->local,
            'local_label'    => ucfirst($destination->local),
            'meta'           => $destination->meta,
            'location'       => $destination->country.($destination->city ? ', '.$destination->city : ''),
            'featured'       => $destination->featured,
            'status'         => $destination->status,
            'status_label'   => ucfirst($destination->status),
            'created_at'     => $destination->created_at->format('d-m-Y'),
            'edit_url'       => route('crm.destinations.edit', $destination->id),
            'destroy_url'    => route('crm.destinations.destroy', $destination->id),
            'toggle_url'     => route('crm.destinations.toggle-status', $destination->id),
        ];
    }

    // Shared store/update field prep: validated data + featured flag + file uploads
    private function prepareData(DestinationRequest $request, ?Destination $destination = null): array
    {
        $data = $request->validated();
        unset($data['cover_image'], $data['gallery_images']);

        $data['featured'] = $request->boolean('featured');

        if ($request->hasFile('cover_image')) {
            if ($destination?->cover_image) {
                Storage::disk('public')->delete($destination->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('destinations', 'public');
        }

        if ($request->hasFile('gallery_images')) {
            if ($destination?->gallery_images) {
                Storage::disk('public')->delete($destination->gallery_images);
            }
            $data['gallery_images'] = collect($request->file('gallery_images'))
                ->map(fn ($file) => $file->store('destinations/gallery', 'public'))
                ->all();
        }

        return $data;
    }

    private function deleteImages(Destination $destination): void
    {
        if ($destination->cover_image) {
            Storage::disk('public')->delete($destination->cover_image);
        }
        if ($destination->gallery_images) {
            Storage::disk('public')->delete($destination->gallery_images);
        }
    }
}
