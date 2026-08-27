<?php

namespace App\Models;

use App\Enums\BedType;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingHotel extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'booking_id',
        'hotel_id',
        'hotel_room_type_id',
        'room_type_name',
        'bed_type',
        'name',
        'unit_price',
        'nights',
        'line_total',
        'day_number',
        'stay_date',
    ];

    protected function casts(): array
    {
        return [
            'stay_date' => 'date',
            'bed_type' => BedType::class,
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function hotelRoomType(): BelongsTo
    {
        return $this->belongsTo(HotelRoomType::class);
    }
}
