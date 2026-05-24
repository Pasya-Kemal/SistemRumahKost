<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'gender',
        'address',
        'emergency_contact',
        'status',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'tenant_id', 'id');
    }
}
