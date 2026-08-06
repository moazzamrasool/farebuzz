<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\HolidayPackage;
use App\Models\PackageReview;
use App\Models\UserQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $kpis = [
            'packages'     => HolidayPackage::count(),
            'active'       => HolidayPackage::where('status', 'active')->count(),
            'destinations' => Destination::count(),
            'hotels'       => Hotel::count(),
            'reviews'      => PackageReview::count(),
            'avg_rating'   => round((float) PackageReview::avg('rating'), 1) ?: null,
        ];

        $packagesByDestination = Destination::withCount('holidayPackages')
            ->having('holiday_packages_count', '>', 0)
            ->orderByDesc('holiday_packages_count')
            ->get(['id', 'name']);

        $statusBreakdown = [
            'active'   => $kpis['active'],
            'inactive' => $kpis['packages'] - $kpis['active'],
        ];

        $reviewsByMonth = PackageReview::selectRaw("DATE_FORMAT(review_date, '%Y-%m') as ym, COUNT(*) as total")
            ->whereNotNull('review_date')
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->map(fn ($row) => [
                'label' => \Carbon\Carbon::createFromFormat('Y-m', $row->ym)->format('M Y'),
                'total' => $row->total,
            ]);

        // "Most selling" — there's no order/booking system yet, so packages are ranked
        // by the strongest sales-adjacent signals we do have: the Best Seller flag,
        // then guest rating, then review volume.
        $topPackages = HolidayPackage::with(['destination:id,name', 'photos'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('is_best_seller')
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('reviews_count')
            ->take(5)
            ->get();

        $recentReviews = PackageReview::with('holidayPackage:id,title')
            ->whereHas('holidayPackage')
            ->latest('review_date')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'kpis', 'packagesByDestination', 'statusBreakdown',
            'reviewsByMonth', 'topPackages', 'recentReviews'
        ));
    }

    public function setting()
    {
        return view('admin.setting');
    }

    public function page()
    {
        $pages = DB::table('pages')->get();
        return view('admin.page', compact('pages'));
    }

    public function pagecreate(Request $request, $id = null)
    {
        $pages = null;

        if (count($request->all()) > 0 && $id) {
            DB::table('pages')->where('id', $id)->update([
                'page_name'    => $request->page_name,
                'page_url'     => $request->page_url,
                'page_title'   => $request->page_title,
                'page_keyword' => $request->page_keyword,
                'page_desc'    => $request->page_desc,
                'page_link'    => $request->page_link,
                'status'       => $request->status,
            ]);
            return response()->json(['status' => 'success', 'message' => 'Page updated successfully.']);
        }

        if (count($request->all()) > 0 && !$id) {
            DB::table('pages')->insert([
                'page_name'    => $request->page_name,
                'page_url'     => $request->page_url,
                'page_title'   => $request->page_title,
                'page_keyword' => $request->page_keyword,
                'page_desc'    => $request->page_desc,
                'page_link'    => $request->page_link,
                'status'       => $request->status,
            ]);
            return response()->json(['status' => 'success', 'message' => 'Page created successfully.']);
        }

        return view('admin.create_page', compact('pages'));
    }

    public function pageEdit(Request $request, $id)
    {
        $pages = DB::table('pages')->where('id', $id)->first();
        return view('admin.create_page', compact('pages'));
    }

    public function query()
    {
        $queries = UserQuery::with('state', 'city')->get();
        return view('admin.query', compact('queries'));
    }
}
