<?php

namespace App\Models\Concerns;

use App\Models\HolidayPackage;
use App\Models\Hotel;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function (Model $model) {
            if (!empty($model->unique_id)) {
                return;
            }

            // Child rows (photos, itineraries, FAQs, ...) must inherit their parent
            // package's tenant rather than the current admin's — otherwise a Super
            // Admin editing a tenant's package on their behalf creates rows with no
            // unique_id, which then vanish from that tenant's own scoped queries.
            if ($model->holiday_package_id) {
                $parent = HolidayPackage::withoutGlobalScopes()->find($model->holiday_package_id);
                if ($parent && $parent->unique_id) {
                    $model->unique_id = $parent->unique_id;
                    return;
                }
            }

            // Same reasoning as above, for hotel bookings: a standalone hotel booking is
            // created from a public (guest) checkout with no admin session, so it must
            // inherit its tenant from the hotel being booked rather than from Auth.
            if ($model->hotel_id) {
                $parent = Hotel::withoutGlobalScopes()->find($model->hotel_id);
                if ($parent && $parent->unique_id) {
                    $model->unique_id = $parent->unique_id;
                    return;
                }
            }

            $admin = Auth::guard('admin')->user();

            if ($admin && !$admin->isSuperAdmin()) {
                $model->unique_id = $admin->unique_id;
            }
        });
    }

    public function scopeWithoutTenantScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope(TenantScope::class);
    }
}
