<?php

namespace App\Models;

use App\Enums\LeadStatus;
use App\Models\Admin\Admin;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Auth;

class PackageEnquiry extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'holiday_package_id',
        'user_id',
        'assigned_admin_id',
        'name',
        'email',
        'phone',
        'travel_date',
        'travellers',
        'budget',
        'source',
        'message',
        'lost_reason',
        'status',
        'next_follow_up_at',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'travel_date' => 'date',
            'status' => LeadStatus::class,
            'budget' => 'decimal:2',
            'next_follow_up_at' => 'datetime',
            'last_activity_at' => 'datetime',
        ];
    }

    public function holidayPackage(): BelongsTo
    {
        return $this->belongsTo(HolidayPackage::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_admin_id');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(LeadActivity::class, 'leadable')->latest();
    }

    public function followUps(): MorphMany
    {
        return $this->morphMany(LeadFollowUp::class, 'leadable');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class)->latest();
    }

    public function pendingFollowUp(): MorphOne
    {
        return $this->morphOne(LeadFollowUp::class, 'leadable')
            ->where('status', 'pending')
            ->ofMany('due_at', 'min');
    }

    public function logActivity(string $type, string $description, ?array $meta = null, ?string $channel = null): LeadActivity
    {
        $activity = $this->activities()->create([
            'admin_id' => Auth::guard('admin')->id(),
            'type' => $type,
            'channel' => $channel,
            'description' => $description,
            'meta' => $meta,
        ]);

        $this->forceFill(['last_activity_at' => now()])->save();

        return $activity;
    }

    public function changeStatus(LeadStatus $new, ?string $lostReason = null): void
    {
        $old = $this->status;

        $this->status = $new;
        $this->lost_reason = $new->requiresLostReason() ? $lostReason : null;
        $this->save();

        $this->logActivity(
            'status_change',
            "Status changed from {$old->label()} to {$new->label()}",
            ['old_status' => $old->value, 'new_status' => $new->value]
        );
    }

    public function assignTo(?Admin $admin): void
    {
        $old = $this->assignedAdmin;

        $this->assigned_admin_id = $admin?->id;
        $this->save();

        $this->logActivity(
            'assignment_change',
            $admin
                ? "Lead assigned to {$admin->name}".($old ? " (previously {$old->name})" : '')
                : 'Lead unassigned'.($old ? " (previously {$old->name})" : ''),
            ['old_admin_id' => $old?->id, 'new_admin_id' => $admin?->id]
        );
    }

    // tier=user only sees leads assigned to them; admin/super_admin see everything
    // TenantScope already lets them see (their own tenant / every tenant).
    public function scopeVisibleTo(Builder $query, Admin $admin): Builder
    {
        if ($admin->isUser()) {
            $query->where('assigned_admin_id', $admin->id);
        }

        return $query;
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeSource(Builder $query, ?string $source): Builder
    {
        return $source ? $query->where('source', $source) : $query;
    }

    public function scopeAssignedTo(Builder $query, ?string $assignedAdminId): Builder
    {
        return $assignedAdminId ? $query->where('assigned_admin_id', $assignedAdminId) : $query;
    }

    public function scopeBetweenDates(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }
}
