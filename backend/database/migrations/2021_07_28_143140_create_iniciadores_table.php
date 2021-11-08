<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIniciadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iniciadores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('id_tipo_entidad')->constrained('tipos_entidad'); //TODO cambiar para seguir la nomenclatura?
            $table->string('nombre');
            $table->string('apellido')->nullable();
            $table->string('telefono')->nullable();
            $table->bigInteger('dni')->nullable();
            $table->bigInteger('cuil')->nullable();
            $table->bigInteger('cuit')->nullable();
            $table->string('area_reparticiones')->nullable();
            $table->string('email')->nullable();
            $table->string('direccion')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('iniciadores');
    }
}
