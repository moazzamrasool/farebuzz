<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\HolidayPackage;
use App\Support\SiteTenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

// Three sample coupons — one percentage (with a cap), one fixed amount, one scoped to a
// single package — so the Coupons module and the homepage Offers cards it feeds
// (see HomepageContentSeeder::offers()) look complete out of the box.
class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $goaPackage = HolidayPackage::where('slug', Str::slug('Goa Beach Escape'))->first();

        $this->coupon('WELCOME10', [
            'title' => 'Flat 10% off on your first booking',
            'description' => 'Get 10% off (up to ₹1,000) on any holiday package or hotel booking.',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'max_discount_amount' => 1000,
            'min_booking_amount' => 3000,
            'applicable_to' => 'all',
            'usage_limit' => 500,
            'per_user_limit' => 1,
        ]);

        $this->coupon('FLAT500', [
            'title' => 'Flat ₹500 off on hotel bookings',
            'description' => 'Save a flat ₹500 on hotel bookings above ₹5,000.',
            'discount_type' => 'fixed',
            'discount_value' => 500,
            'min_booking_amount' => 5000,
            'applicable_to' => 'hotels',
            'usage_limit' => 200,
            'per_user_limit' => 1,
        ]);

        if ($goaPackage) {
            $this->coupon('GOA15', [
                'title' => 'Goa Beach Escape — 15% off',
                'description' => 'Exclusive 15% discount (up to ₹2,500) on the Goa Beach Escape package.',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'max_discount_amount' => 2500,
                'min_booking_amount' => 0,
                'applicable_to' => 'specific_packages',
                'applicable_ids' => [$goaPackage->id],
                'usage_limit' => 100,
                'per_user_limit' => 1,
            ]);
        }
    }

    private function coupon(string $code, array $attrs): Coupon
    {
        return Coupon::withoutTenantScope()->updateOrCreate(
            ['unique_id' => SiteTenant::id(), 'code' => $code],
            array_merge([
                'valid_from' => Carbon::today(),
                'valid_to' => Carbon::today()->addMonths(6),
                'status' => 'active',
            ], $attrs)
        );
    }
}
