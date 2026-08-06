<?php

namespace App\Models\Concerns;

use App\Support\SiteTenant;
use Illuminate\Database\Eloquent\Builder;

/**
 * Explicit frontend-side tenant filter. Unlike BelongsToTenant's global scope
 * (which only applies for an authenticated admin and no-ops for guests), this
 * is opt-in per query so public controllers make the single-tenant filtering
 * visible: Model::forSite()->... Swap this method's body when multi-company
 * frontend support is added — nothing else needs to change.
 */
trait ForSiteTenant
{
    public function scopeForSite(Builder $query): Builder
    {
        return $query->where($this->getTable().'.unique_id', SiteTenant::id());
    }
}
