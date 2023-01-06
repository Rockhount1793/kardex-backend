<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{

    protected $fillable = [
        'nombre',
        'color',
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
