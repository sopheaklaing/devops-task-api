<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // JWT identifier
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // JWT custom claims
    public function getJWTCustomClaims()
    {
        return [];
    }
}
