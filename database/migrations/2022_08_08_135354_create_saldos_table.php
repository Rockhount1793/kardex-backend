<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaldosTable extends Migration
{
    
    public function up()
    {
        Schema::create('saldos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('producto_id');
            $table->foreignId('ubicacion_id');
            $table->bigInteger('entradas');
            $table->bigInteger('salidas');
            $table->timestamps();
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('saldos');
    }
}
