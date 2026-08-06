<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Deliberately plain, same reasoning as BookingPayment — this row is written from
// PayUService::handleSuccess() during an unauthenticated PayU callback, so there is
// no admin session for BelongsToTenant's creating hook to inherit a tenant from, and
// this model has neither holiday_package_id nor hotel_id for it to fall back on.
// unique_id is copied from the parent booking explicitly and is always accessed via
// Coupon::usages()/Booking, never queried tenant-scoped on its own.
class CouponUsage extends Model
{
    protected $fillable = [
        'unique_id',
        'coupon_id',
        'booking_id',
        'user_id',
        'email',
        'discount_amount',
    ];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
