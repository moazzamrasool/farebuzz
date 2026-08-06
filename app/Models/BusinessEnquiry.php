<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessEnquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_name',
        'owner_name',
        'email',
        'phone',
        'business_type',
        'message',
        'status',
        'business_id',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
