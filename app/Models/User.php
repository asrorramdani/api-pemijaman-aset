<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // 🔑 WAJIB ADA & PUBLIC
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // 🔑 WAJIB ADA & PUBLIC
    public function getJWTCustomClaims()
    {
        return [];
    }

    // RELATION
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
