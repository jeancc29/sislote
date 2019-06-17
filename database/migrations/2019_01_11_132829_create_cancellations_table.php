<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCancellationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cancellations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('idTicket');
            $table->unsignedInteger('idUsuario');
            $table->string('razon');
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
        Schema::dropIfExists('cancellations');
    }
}
