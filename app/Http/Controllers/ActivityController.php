<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityCategory;
use App\Models\Destination;
use App\Models\ListingPageSeo;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::forSite()->where('status', 'active')->with(['destination', 'travelCategory', 'activityCategory']);

        if ($request->filled('destination_id')) {
            $query->where('destination_id', $request->get('destination_id'));
        } elseif ($request->filled('destination')) {
            $term = $request->get('destination');
            $query->whereHas('destination', fn ($d) => $d->where('name', 'like', "%{$term}%")->orWhere('city', 'like', "%{$term}%"));
        }

        if ($request->filled('activity_category_id')) {
            $query->where('activity_category_id', $request->get('activity_category_id'));
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
        $categories = ActivityCategory::forSite()->where('status', 'active')->orderBy('sort_order')->orderBy('name')->get();
        $destinations = Destination::forSite()->where('status', 'active')->orderBy('name')->get();
        $pageSeo = ListingPageSeo::forSitePage('activities');

        return view('activities.index', compact('activities', 'categories', 'destinations', 'pageSeo'));
    }

    public function show(Activity $activity)
    {
        abort_unless($activity->status === 'active', 404);

        $activity->load(['destination', 'travelCategory', 'activityCategory']);
        $related = Activity::forSite()->where('status', 'active')->where('id', '!=', $activity->id)
            ->where(function ($q) use ($activity) {
                $q->where('destination_id', $activity->destination_id);
                if ($activity->activity_category_id) {
                    $q->orWhere('activity_category_id', $activity->activity_category_id);
                }
            })
            ->limit(4)->get();

        return view('activities.show', compact('activity', 'related'));
    }
}
