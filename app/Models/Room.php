<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * protected Mass Assign
     */
    protected $fillable = [
        'kost_id',
        'room_name',
        'price',
        'description',
        'is_available',
    ];


    protected $casts = [
        'is_available' => 'boolean',
        'price'        => 'integer',
    ];

    /**
     * Relation:Rooom-Kost (Inverse of One-to-Many)
     */
    public function kost()
    {
        return $this->belongsTo(Kost::class, 'kost_id', 'id');
    }

    /**
     * Relation: Room-Photos (One to Many)
     */
    public function photos()
    {
        return $this->hasMany(Photo::class, 'room_id', 'id');
    }

    /**
     * Ralation: Room-Booking (One-to-Many)
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'room_id', 'id');
    }

    /**
     * Booted method handle protected state
     */
    protected static function booted()
    {
        // Event listener: Before Rooms Delete
        static::deleting(function ($room) {
            
            $hasActiveBooking = $room->bookings()->where('status', 'active')->exists();
            
            if ($hasActiveBooking) {
                throw new \Exception('Kamar tidak dapat dihapus karena masih ada penyewa aktif. Selesaikan atau batalkan sewa terlebih dahulu.');
            }
            $room->bookings()->where('status', 'pending')->delete();

        });
    }
}