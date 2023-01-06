<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{

    public function up()
    {
        Schema::create('productos', function (Blueprint $table){
            $table->id();
            $table->foreignId('user_id');
            $table->integer('margen_ganancia')->default(30);
            $table->string('nombre',100);
            $table->string('marca',100);
            $table->string('referencia',100);
            $table->string('codigo',100);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('productos');
    }

}
