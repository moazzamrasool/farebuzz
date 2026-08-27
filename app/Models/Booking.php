<?php

namespace App\Models;

use App\Enums\BedType;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'holiday_package_id', 'booking_type', 'user_id', 'booking_reference',
        'package_title', 'package_slug', 'departure_city', 'travel_date',
        'room_type_name', 'room_type_price', 'room_type_original_price', 'room_type_discount_percent', 'room_type_bed_type', 'adults', 'children',
        'hotel_id', 'hotel_room_type_id', 'check_in_date', 'check_out_date', 'nights', 'rooms',
        'traveller_name', 'traveller_email', 'traveller_phone', 'traveller_address',
        'special_requests', 'gst_number',
        'base_fare', 'taxes_fee', 'discount_amount', 'activities_total', 'hotels_total', 'total_amount',
        'coupon_id', 'coupon_code', 'coupon_discount_amount',
        'status', 'payment_status',
    ];

    protected function casts(): array
    {
        return [
            'travel_date' => 'date',
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'room_type_bed_type' => BedType::class,
        ];
    }

    // Every route that binds a Booking (public, user dashboard, and admin) resolves by
    // this human-readable reference instead of the numeric id — keeps URLs consistent
    // and avoids leaking sequential ids.
    public function getRouteKeyName(): string
    {
        return 'booking_reference';
    }

    public function holidayPackage(): BelongsTo
    {
        return $this->belongsTo(HolidayPackage::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function hotelRoomType(): BelongsTo
    {
        return $this->belongsTo(HotelRoomType::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(BookingPayment::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(BookingActivity::class);
    }

    public function hotels(): HasMany
    {
        return $this->hasMany(BookingHotel::class);
    }

    public function latestPayment(): HasMany
    {
        return $this->payments()->latest();
    }

    // FB-20260711-A1B2C3 — human-readable, collision-checked (withoutGlobalScopes so it's
    // unique across every tenant, matching the DB's own unique index on the column).
    public static function generateReference(): string
    {
        do {
            $reference = 'FB-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (static::withoutGlobalScopes()->where('booking_reference', $reference)->exists());

        return $reference;
    }
}
