<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    protected $fillable = [
        'user_id',
        'producto_id',
        'ubicacion_id',
        'entradas',
        'salidas'
    ];

    protected $hidden = [
        'user_id',
        'updated_at',
        'created_at'
    ];

    protected $casts = [
        '' => '',
    ];
}
