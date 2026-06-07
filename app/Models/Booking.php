<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Mass Assignment Protection
     */
    protected $fillable = [
        'tenant_id',
        'room_id',
        'start_date',
        'duration_months',
        'status',
    ];

    protected $casts = [
        'start_date'      => 'date',
        'duration_months' => 'integer',
    ];

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id', 'id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'booking_id', 'id');
    }

    protected static function booted()
    {
        static::updating(function ($booking) {
            if ($booking->isDirty('status') && $booking->status === 'completed') {
                $booking->room()->update(['is_available' => true]);
            }

            if ($booking->isDirty('status') && $booking->getOriginal('status') === 'active' && $booking->status === 'pending') {
                $booking->room()->update(['is_available' => true]);
            }
        });

        static::deleting(function ($booking) {
            if ($booking->status === 'active') {
                $booking->room()->update(['is_available' => true]);
            }

            $booking->payments()->where('status', 'unpaid')->delete();
        });
    }
}
