<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    // Auth::guard('admin')->user() resolves the logged-in Admin by querying this very
    // model, which re-applies this scope, which calls user() again — infinite
    // recursion. This flag breaks the cycle: the query that resolves "who is the
    // current admin" must run unscoped anyway (you can't know the tenant before you
    // know who's logged in).
    private static bool $resolving = false;

    public function apply(Builder $builder, Model $model): void
    {
        if (self::$resolving) {
            return;
        }

        self::$resolving = true;
        try {
            $admin = Auth::guard('admin')->user();
        } finally {
            self::$resolving = false;
        }

        // No authenticated admin, or Super Admin — full bypass, no WHERE clause added.
        if (!$admin || $admin->isSuperAdmin()) {
            return;
        }

        $builder->where($model->getTable().'.unique_id', $admin->unique_id);
    }
}
