<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProveedorasTable extends Migration
{

    public function up()
    {
        Schema::create('proveedoras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('nombre',100);
            $table->string('contacto',100);
            $table->string('email',100);
            $table->string('direccion',100);
            $table->string('telefono',100);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proveedoras');
    }

}
