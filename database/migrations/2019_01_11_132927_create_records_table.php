<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('idTicket');
            $table->integer('transaccionTipo');
            $table->unsignedInteger('idUsuario');
            $table->timestamps();

            $table->foreign('idTicket')->references('id')->on('tickets');
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
        $table->dropForeign(['idTicket']);
        $table->dropForeign(['idUsuario']);
        Schema::dropIfExists('records');
    }
}
