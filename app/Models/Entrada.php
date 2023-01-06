<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    
    protected $fillable = ['user_id','producto_id','proveedor_id','ubicacion_id','costo_unidad','cantidad','pedido'];
    
    protected $hidden = ['user_id','updated_at'];

    protected $casts = [''=>''];

}
