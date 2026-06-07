<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'created_by_owner_id',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function tenant()
    {
        return $this->hasOne(Tenant::class, 'user_id', 'id');
    }

    /** The Owner who created this Admin (null for owners, tenants, super admins). */
    public function creatorOwner()
    {
        return $this->belongsTo(User::class, 'created_by_owner_id', 'id');
    }

    /** Admin accounts created by this Owner. Only meaningful when $this is an Owner. */
    public function managedAdmins()
    {
        return $this->hasMany(User::class, 'created_by_owner_id', 'id');
    }

    /**
     * Returns the owner_id used to scope data for this user.
     *  - Owner   → their own id
     *  - Admin   → the owner who created them (null = super admin, no scoping)
     *  - Tenant  → null (not applicable)
     */
    public function effectiveOwnerId(): ?int
    {
        $roleName = optional($this->role)->role_name;
        if ($roleName === 'owner') return $this->id;
        if ($roleName === 'admin') return $this->created_by_owner_id;
        return null;
    }
}
