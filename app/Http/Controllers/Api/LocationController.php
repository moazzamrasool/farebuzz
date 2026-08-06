<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;

// Backs the hero search widget's city/destination autocomplete fields.
// Reuses the Destinations master (no separate Cities table) — see plan.
class LocationController extends Controller
{
    public function search(Request $request)
    {
        $term = trim((string) $request->get('q', ''));
        $limit = min((int) $request->get('limit', 8), 20);

        $results = Destination::forSite()
            ->where('status', 'active')
            ->when($term !== '', fn ($query) => $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$term}%")
                ->orWhere('city', 'like', "%{$term}%")
                ->orWhere('country', 'like', "%{$term}%")
            ))
            ->orderByDesc('featured')
            ->limit($limit)
            ->get(['id', 'name', 'city', 'country']);

        return response()->json($results->map(fn ($destination) => [
            'id' => $destination->id,
            'label' => $destination->name,
            'sub' => trim(collect([$destination->city, $destination->country])->filter()->join(', ')),
        ]));
    }
}
