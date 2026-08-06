<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_enquiry_id',
        'name',
        'owner_name',
        'email',
        'phone',
        'business_type',
        'unique_id',
        'status',
    ];

    public function enquiry(): HasOne
    {
        return $this->hasOne(BusinessEnquiry::class);
    }
}
