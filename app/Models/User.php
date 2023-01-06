<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'alias',
        'telefono',
        'avatar'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'recovery',
        'created_at'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

}
