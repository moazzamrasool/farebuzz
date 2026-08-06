<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CouponRequest;
use App\Models\Coupon;
use App\Models\HolidayPackage;
use App\Models\Hotel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::withCount('usages')->latest()->paginate(15);

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create', $this->formOptions());
    }

    public function store(CouponRequest $request)
    {
        $data = $this->prepareData($request);
        Coupon::create($data);

        return redirect()->route('crm.coupons.index')->with('success', 'Coupon created successfully.');
    }

    public function show(Coupon $coupon)
    {
        $coupon->loadCount('usages');
        $totalDiscountGiven = $coupon->usages()->sum('discount_amount');
        $usages = $coupon->usages()->with('booking')->latest()->paginate(15);

        return view('admin.coupons.show', compact('coupon', 'totalDiscountGiven', 'usages'));
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', array_merge(['coupon' => $coupon], $this->formOptions()));
    }

    public function update(CouponRequest $request, Coupon $coupon)
    {
        $data = $this->prepareData($request, $coupon);
        $coupon->update($data);

        return redirect()->route('crm.coupons.edit', $coupon->id)->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        if ($coupon->usages()->exists()) {
            return redirect()->route('crm.coupons.index')
                ->with('error', 'Cannot delete this coupon — it has already been used on one or more bookings.');
        }

        if ($coupon->banner_image) {
            Storage::disk('public')->delete($coupon->banner_image);
        }
        $coupon->delete();

        return redirect()->route('crm.coupons.index')->with('success', 'Coupon deleted successfully.');
    }

    public function toggleStatus(Coupon $coupon)
    {
        $coupon->update([
            'status' => $coupon->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->route('crm.coupons.index')->with('success', 'Status updated successfully.');
    }

    private function formOptions(): array
    {
        return [
            'packages' => HolidayPackage::orderBy('title')->get(['id', 'title']),
            'hotels'   => Hotel::orderBy('name')->get(['id', 'name']),
        ];
    }

    private function prepareData(CouponRequest $request, ?Coupon $coupon = null): array
    {
        $data = $request->validated();
        $data['code'] = Str::upper(trim($data['code']));

        if (!in_array($data['applicable_to'], ['specific_packages', 'specific_hotels'], true)) {
            $data['applicable_ids'] = null;
        }

        if ($request->hasFile('banner_image')) {
            if ($coupon?->banner_image) {
                Storage::disk('public')->delete($coupon->banner_image);
            }
            $data['banner_image'] = $request->file('banner_image')->store('coupons', 'public');
        } else {
            unset($data['banner_image']);
        }

        return $data;
    }
}
