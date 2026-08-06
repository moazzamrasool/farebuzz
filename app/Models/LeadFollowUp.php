<?php

namespace App\Models;

use App\Models\Admin\Admin;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LeadFollowUp extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'leadable_type',
        'leadable_id',
        'admin_id',
        'assigned_admin_id',
        'due_at',
        'note',
        'status',
        'completed_at',
        'reminder_sent',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
            'reminder_sent' => 'boolean',
        ];
    }

    public function leadable(): MorphTo
    {
        return $this->morphTo();
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_admin_id');
    }

    public function scopeDueToday(Builder $query): Builder
    {
        return $query->where('status', 'pending')->whereDate('due_at', now()->toDateString());
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'pending')->where('due_at', '<', now());
    }

    // tier=user only sees follow-ups assigned to them; admin/super_admin see the
    // whole tenant (TenantScope already narrows super_admin to nothing extra, i.e.
    // everything, and admin to their own tenant).
    public function scopePendingFor(Builder $query, Admin $admin): Builder
    {
        $query->where('status', 'pending');

        if ($admin->isUser()) {
            $query->where('assigned_admin_id', $admin->id);
        }

        return $query;
    }

    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->leadable?->logActivity(
            'follow_up_completed',
            'Follow-up completed: '.($this->note ?: 'No note provided'),
            ['follow_up_id' => $this->id]
        );
    }
}
