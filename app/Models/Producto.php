<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{

    protected $fillable = [
        'user_id',
        'margen_ganancia',
        'nombre',
        'marca',
        'referencia',
        'codigo'
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
