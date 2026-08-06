<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Deliberately plain — always accessed via Booking::payments(), so no BelongsToTenant
// global scope; unique_id is copied from the parent booking for reporting only.
class BookingPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'unique_id', 'booking_id', 'gateway', 'mode',
        'gateway_txn_id', 'gateway_payment_id', 'amount', 'currency',
        'status', 'request_payload', 'response_payload', 'hash_verified',
    ];

    protected function casts(): array
    {
        return [
            'request_payload' => 'array',
            'response_payload' => 'array',
            'hash_verified' => 'boolean',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
