<?php

namespace App\Models\Admin;

use App\Models\Concerns\BelongsToTenant;
use App\Models\PackageEnquiry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, BelongsToTenant;

    // Uses separate 'admins' table — completely independent from users
    protected $table = 'admins';

    protected $guard_name = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'unique_id',
        'tier',
        'created_by',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_blocked'        => 'boolean',
            'blocked_at'        => 'datetime',
            'unblocked_at'      => 'datetime',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->tier === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->tier === 'admin';
    }

    public function isUser(): bool
    {
        return $this->tier === 'user';
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(self::class, 'created_by');
    }

    public function createdAdmins(): HasMany
    {
        return $this->hasMany(self::class, 'created_by');
    }

    public function assignedLeads(): HasMany
    {
        return $this->hasMany(PackageEnquiry::class, 'assigned_admin_id');
    }

    public function blockedBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'blocked_by');
    }

    public function unblockedBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'unblocked_by');
    }

    // Super Admin never suspended; an Admin row IS the company; a sub-admin (User)
    // inherits suspension from the Admin row sharing the same unique_id.
    public function isCompanySuspended(): bool
    {
        if ($this->isSuperAdmin()) {
            return false;
        }

        if ($this->isAdmin()) {
            return $this->status !== 'active' || $this->is_blocked;
        }

        return static::isUniqueIdBlocked($this->unique_id);
    }

    // Cached blocked-status lookup for a company's unique_id, independent of any
    // authenticated admin — used by both the CRM (sub-admin login) and the public
    // frontend (App\Http\Middleware\EnsureSiteNotBlocked, resolved from config, no
    // auth session at all). Short TTL so Super Admin's block/unblock action takes
    // effect within a minute even without the explicit Cache::forget() this app's
    // controllers already issue on every block/unblock/delete.
    public static function isUniqueIdBlocked(?string $uniqueId): bool
    {
        if (!$uniqueId) {
            return false;
        }

        return Cache::remember(
            "company-status:{$uniqueId}",
            now()->addMinute(),
            fn () => static::withoutGlobalScopes()
                ->where('unique_id', $uniqueId)
                ->where('tier', 'admin')
                ->where(function ($query) {
                    $query->where('status', '!=', 'active')->orWhere('is_blocked', true);
                })
                ->exists()
        );
    }

    public static function forgetBlockedStatusCache(string $uniqueId): void
    {
        Cache::forget("company-status:{$uniqueId}");
    }

    // User-facing explanation shown at the CRM login screen (and on forced logout) when
    // isCompanySuspended() is true. Only queried on that failure path, never per-request.
    public function suspensionMessage(): string
    {
        $reason = $this->isAdmin()
            ? $this->blocked_reason
            : static::withoutGlobalScopes()
                ->where('unique_id', $this->unique_id)
                ->where('tier', 'admin')
                ->value('blocked_reason');

        return $reason
            ? "Your account has been suspended. Reason: {$reason}"
            : 'Your account has been suspended.';
    }
}
