<?php

namespace App\Models;

use App\Models\Admin\Admin;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Quotation extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'quotation_number',
        'package_enquiry_id',
        'holiday_package_id',
        'created_by',
        'customer_name',
        'customer_email',
        'customer_phone',
        'valid_until',
        'notes',
        'total_amount',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'valid_until' => 'date',
            'total_amount' => 'decimal:2',
            'sent_at' => 'datetime',
        ];
    }

    public function packageEnquiry(): BelongsTo
    {
        return $this->belongsTo(PackageEnquiry::class);
    }

    public function holidayPackage(): BelongsTo
    {
        return $this->belongsTo(HolidayPackage::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order');
    }

    // QT-20260730-A1B2C3 — human-readable, collision-checked across every tenant
    // (withoutGlobalScopes), same pattern as Booking::generateReference().
    public static function generateNumber(): string
    {
        do {
            $number = 'QT-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (static::withoutGlobalScopes()->where('quotation_number', $number)->exists());

        return $number;
    }
}
