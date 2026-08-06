<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Destination;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::forSite()->where('status', 'active')->with(['destination', 'travelCategory']);

        if ($request->filled('destination_id')) {
            $query->where('destination_id', $request->get('destination_id'));
        } elseif ($request->filled('destination')) {
            $term = $request->get('destination');
            $query->whereHas('destination', fn ($d) => $d->where('name', 'like', "%{$term}%")->orWhere('city', 'like', "%{$term}%"));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->get('price_min'));
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->get('price_max'));
        }

        match ($request->get('sort')) {
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            default => $query->orderBy('sort_order'),
        };

        $activities = $query->paginate(12)->withQueryString();
        $categories = Activity::forSite()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
        $destinations = Destination::forSite()->where('status', 'active')->orderBy('name')->get();

        return view('activities.index', compact('activities', 'categories', 'destinations'));
    }

    public function show(Activity $activity)
    {
        abort_unless($activity->status === 'active', 404);

        $activity->load(['destination', 'travelCategory']);
        $related = Activity::forSite()->where('status', 'active')->where('id', '!=', $activity->id)
            ->where(fn ($q) => $q->where('destination_id', $activity->destination_id)->orWhere('category', $activity->category))
            ->limit(4)->get();

        return view('activities.show', compact('activity', 'related'));
    }
}
