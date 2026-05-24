<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use HasFactory;


    protected $fillable = [
        'room_id',
        'image_path',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     *Relation: Photo-Room (Inverse of One-to-Many)
     */
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }

    /**
     * Booted method for Database and Filesystem sync
     */
    protected static function booted()
    {
        // Event listener: before deleting row
        static::deleting(function ($photo) {
            if (Storage::disk('public')->exists($photo->image_path)) {
                Storage::disk('public')->delete($photo->image_path);
            }
        });
    }
}