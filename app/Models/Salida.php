<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    protected $fillable = [
        'user_id',
        'producto_id',
        'ubicacion_id',
        'costo_unidad',
        'cantidad',
        'pedido'
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
