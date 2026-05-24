<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kost extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Protected data
     */
    protected $fillable = [
        'owner_id',
        'kost_name',
        'kost_gender',
        'address',
    ];

    /**
     * Relation: Kost-User: one to one (Owner)
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    /**
     * Relation: Kost-Room: one to many
     */
    public function rooms()
    {
        return $this->hasMany(Room::class, 'kost_id', 'id');
    }

    /**
     * Booted method handle model event.
     */
    protected static function booted()
    {
        // Event listener: execute instance Kost on soft-delete
        static::deleted(function ($kost) {
            $kost->rooms()->delete();
        });

        // Event listener : if Owner  'restore' on Kost data, 
        // return all room data kamar related.
        static::restored(function ($kost) {
            $kost->rooms()->withTrashed()->restore();
        });
    }
}
