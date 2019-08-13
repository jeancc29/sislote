<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idBanca')->nullable();
            $table->unsignedInteger('idUsuario');
            $table->string('tabla');
            $table->unsignedInteger('idRegistroTablaAccion'); //Este sera el id afectado de la tabla en la cual se ha realizado la accion
            $table->string('accion');
            $table->string('campo');//Campo que se modifico
            $table->string('valor_viejo');
            $table->string('valor_nuevo');
            $table->timestamps();

            $table->foreign('idUsuario')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropForeign(['idUsuario']);
        Schema::dropIfExists('logs');
    }
}
