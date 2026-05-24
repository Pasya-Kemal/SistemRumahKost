<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'room_id',
        'title',
        'description',
        'status',
        'submitted_at',
        'resolved_at',
    ];

    protected $casts = [
        'submitted_at' => 'date',
        'resolved_at' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }
}
