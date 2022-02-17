<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpedienteSiifsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expediente_siifs', function (Blueprint $table) {
            $table->id();
            $table->integer('area_actual_id');
            $table->string('nro_expediente_ext');
            $table->integer('fojas');
            $table->date('fecha');
            $table->text('descripcion');
            $table->string('nombre');
            $table->string('apellido')->nullable();
            $table->unsignedBigInteger('dni')->nullable();
            $table->unsignedBigInteger('cuil')->nullable();
            $table->unsignedBigInteger('cuit')->nullable();
            $table->unsignedBigInteger('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('direccion')->nullable();
            $table->string('area_reparticiones');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expediente_siifs');
    }
}
