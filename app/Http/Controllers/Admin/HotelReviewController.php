<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HotelReviewRequest;
use App\Models\Hotel;
use App\Models\HotelReview;

class HotelReviewController extends Controller
{
    public function index()
    {
        $reviews = HotelReview::with('hotel')->orderBy('sort_order')->latest()->paginate(15);
        $hotels = Hotel::orderBy('name')->get();

        return view('admin.hotel-reviews.index', compact('reviews', 'hotels'));
    }

    public function store(HotelReviewRequest $request)
    {
        $data = $request->validated();
        $data['verified'] = $request->boolean('verified', true);
        $review = HotelReview::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Review created successfully.',
                'review' => $this->toJsonRow($review),
            ]);
        }

        return redirect()->route('crm.hotel-reviews.index')
            ->with('success', 'Review created successfully.');
    }

    public function edit(HotelReview $hotelReview)
    {
        return response()->json([
            'success' => true,
            'review' => [
                'id'                  => $hotelReview->id,
                'hotel_id'            => $hotelReview->hotel_id,
                'reviewer_name'       => $hotelReview->reviewer_name,
                'rating'              => $hotelReview->rating,
                'location_rating'     => $hotelReview->location_rating,
                'cleanliness_rating'  => $hotelReview->cleanliness_rating,
                'service_rating'      => $hotelReview->service_rating,
                'value_rating'        => $hotelReview->value_rating,
                'comment'             => $hotelReview->comment,
                'review_date'         => optional($hotelReview->review_date)->format('Y-m-d'),
                'verified'            => $hotelReview->verified,
                'sort_order'          => $hotelReview->sort_order,
                'update_url'          => route('crm.hotel-reviews.update', $hotelReview->id),
            ],
        ]);
    }

    public function update(HotelReviewRequest $request, HotelReview $hotelReview)
    {
        $data = $request->validated();
        $data['verified'] = $request->boolean('verified');
        $hotelReview->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Review updated successfully.',
                'review' => $this->toJsonRow($hotelReview),
            ]);
        }

        return redirect()->route('crm.hotel-reviews.index')
            ->with('success', 'Review updated successfully.');
    }

    public function destroy(HotelReview $hotelReview)
    {
        $hotelReview->delete();

        return redirect()->route('crm.hotel-reviews.index')
            ->with('success', 'Review deleted successfully.');
    }

    private function toJsonRow(HotelReview $review): array
    {
        $review->loadMissing('hotel');

        return [
            'id'            => $review->id,
            'hotel_name'    => $review->hotel->name,
            'reviewer_name' => $review->reviewer_name,
            'rating'        => $review->rating,
            'review_date'   => $review->review_date->format('d M Y'),
            'verified'      => $review->verified,
            'edit_url'      => route('crm.hotel-reviews.edit', $review->id),
            'destroy_url'   => route('crm.hotel-reviews.destroy', $review->id),
        ];
    }
}
