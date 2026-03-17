<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'can_access_admin',
        'can_access_mobile',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'can_access_admin' => 'boolean',
        'can_access_mobile' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function jobEntries()
    {
        return $this->hasMany(JobEntry::class);
    }

    public function approvedEntries()
    {
        return $this->hasMany(JobEntry::class, 'approved_by');
    }

    public function canAccessAdmin()
    {
        return $this->is_active && $this->can_access_admin;
    }

    public function canAccessMobile()
    {
        return $this->is_active && $this->can_access_mobile;
    }
}