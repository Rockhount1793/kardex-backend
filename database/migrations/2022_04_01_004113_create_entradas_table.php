<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntradasTable extends Migration
{
    
    public function up()
    {
        Schema::create('entradas', function (Blueprint $table){
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('producto_id');
            $table->foreignId('proveedor_id');
            $table->foreignId('ubicacion_id');
            $table->integer('costo_unidad');
            $table->integer('cantidad');
            $table->string('pedido',100);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('entradas');
    }
}
