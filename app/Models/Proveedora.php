<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedora extends Model
{
  
    protected $fillable = [
        'nombre',
        'contacto',
        'email',
        'direccion',
        'telefono',
        'user_id'
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
