<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\ForSiteTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Coupon extends Model
{
    use BelongsToTenant, ForSiteTenant, SoftDeletes;

    protected $fillable = [
        'code',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'max_discount_amount',
        'min_booking_amount',
        'valid_from',
        'valid_to',
        'usage_limit',
        'per_user_limit',
        'applicable_to',
        'applicable_ids',
        'banner_image',
        'banner_link',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'valid_from' => 'date',
            'valid_to' => 'date',
            'applicable_ids' => 'array',
            'discount_value' => 'decimal:2',
            'max_discount_amount' => 'decimal:2',
            'min_booking_amount' => 'decimal:2',
        ];
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    // Status + date-window only — does not check usage/per-user limits, which
    // require a specific user/booking context (see CouponService::validate()).
    public function isCurrentlyValid(): bool
    {
        $today = Carbon::today();

        return $this->status === 'active'
            && $today->between($this->valid_from, $this->valid_to);
    }
}
