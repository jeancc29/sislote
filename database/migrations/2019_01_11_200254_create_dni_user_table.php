<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDniUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dni_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idDni');
            $table->unsignedInteger('idUsuario');
            $table->unsignedInteger('idTipoDni');
            $table->timestamps();

            $table->foreign('idDni')->references('id')->on('dnis');
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
        $table->dropForeign(['idDni']);
        $table->dropForeign(['idUsuario']);
        Schema::dropIfExists('dni_user');
    }
}
