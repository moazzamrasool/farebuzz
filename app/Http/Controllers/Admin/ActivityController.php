<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ActivitiesTemplateExport;
use App\Http\Controllers\Admin\Concerns\HandlesBulkImportResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ActivityRequest;
use App\Imports\ActivitiesImport;
use App\Models\Activity;
use App\Models\Destination;
use App\Models\TravelCategory;
use App\Traits\GeneratesUniqueSlug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ActivityController extends Controller
{
    use GeneratesUniqueSlug, HandlesBulkImportResponse;

    public function index()
    {
        $activities = Activity::with(['destination', 'travelCategory'])->orderBy('sort_order')->latest()->paginate(15);
        [$destinations, $travelCategories] = $this->formOptions();
        return view('admin.activities.index', compact('activities', 'destinations', 'travelCategories'));
    }

    public function create()
    {
        [$destinations, $travelCategories] = $this->formOptions();
        return view('admin.activities.create', compact('destinations', 'travelCategories'));
    }

    public function store(ActivityRequest $request)
    {
        $data = $request->validated();
        unset($data['image']);
        $data['slug'] = $this->generateUniqueSlug(Activity::class, $request->name);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('activities', 'public');
        }

        $activity = Activity::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Activity created successfully.',
                'activity' => $this->toJsonRow($activity),
            ]);
        }

        return redirect()->route('crm.activities.index')
            ->with('success', 'Activity created successfully.');
    }

    public function edit(Activity $activity)
    {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'activity' => [
                    'id'                 => $activity->id,
                    'destination_id'     => $activity->destination_id,
                    'travel_category_id' => $activity->travel_category_id,
                    'name'           => $activity->name,
                    'category'       => $activity->category,
                    'description'    => $activity->description,
                    'duration'       => $activity->duration,
                    'price'          => $activity->price,
                    'status'         => $activity->status,
                    'sort_order'     => $activity->sort_order,
                    'image_url'      => $activity->image ? asset('storage/'.$activity->image) : null,
                    'update_url'     => route('crm.activities.update', $activity->id),
                ],
            ]);
        }

        [$destinations, $travelCategories] = $this->formOptions();
        return view('admin.activities.edit', compact('activity', 'destinations', 'travelCategories'));
    }

    public function update(ActivityRequest $request, Activity $activity)
    {
        $data = $request->validated();
        unset($data['image']);

        if ($activity->name !== $request->name) {
            $data['slug'] = $this->generateUniqueSlug(Activity::class, $request->name, $activity->id);
        }

        if ($request->hasFile('image')) {
            if ($activity->image) {
                Storage::disk('public')->delete($activity->image);
            }
            $data['image'] = $request->file('image')->store('activities', 'public');
        }

        $activity->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Activity updated successfully.',
                'activity' => $this->toJsonRow($activity),
            ]);
        }

        return redirect()->route('crm.activities.index')
            ->with('success', 'Activity updated successfully.');
    }

    public function destroy(Activity $activity)
    {
        if ($activity->holidayPackages()->exists() || $activity->optionalOnPackages()->exists()) {
            return redirect()->route('crm.activities.index')
                ->with('error', 'Cannot delete this activity — it is still linked to one or more holiday packages.');
        }

        if ($activity->image) {
            Storage::disk('public')->delete($activity->image);
        }

        $activity->delete();

        return redirect()->route('crm.activities.index')
            ->with('success', 'Activity deleted successfully.');
    }

    public function toggleStatus(Activity $activity)
    {
        $activity->update([
            'status' => $activity->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->route('crm.activities.index')
            ->with('success', 'Status updated successfully.');
    }

    public function downloadTemplate()
    {
        return Excel::download(new ActivitiesTemplateExport, 'activities-template.xlsx');
    }

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $import = new ActivitiesImport;
        Excel::import($import, $request->file('file'));

        return $this->bulkImportResponse($import->importedCount(), $import->rowErrors());
    }

    public function downloadErrorReport(string $token)
    {
        return $this->streamImportErrorReport($token);
    }

    protected function bulkUploadErrorRouteName(): string
    {
        return 'crm.activities.bulk-upload.errors';
    }

    // Shared JSON shape used to render/update a table row after store/update
    private function toJsonRow(Activity $activity): array
    {
        $activity->loadMissing('destination', 'travelCategory');

        return [
            'id'           => $activity->id,
            'image_url'    => $activity->image ? asset('storage/'.$activity->image) : null,
            'name'         => $activity->name,
            'destination_name' => $activity->destination->name ?? 'N/A',
            'category'     => $activity->category ?? 'N/A',
            'travel_category_name' => $activity->travelCategory->name ?? 'N/A',
            'price'        => $activity->price ? number_format($activity->price, 2) : 'N/A',
            'status'       => $activity->status,
            'status_label' => ucfirst($activity->status),
            'edit_url'     => route('crm.activities.edit', $activity->id),
            'destroy_url'  => route('crm.activities.destroy', $activity->id),
            'toggle_url'   => route('crm.activities.toggle-status', $activity->id),
        ];
    }

    private function formOptions(): array
    {
        return [
            Destination::where('status', 'active')->orderBy('name')->get(),
            TravelCategory::where('status', 'active')->orderBy('name')->get(),
        ];
    }
}
