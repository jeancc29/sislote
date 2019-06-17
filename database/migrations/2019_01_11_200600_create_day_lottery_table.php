<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDayLotteryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('day_lottery', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idDia');
            $table->unsignedInteger('idLoteria');
            $table->time('horaApertura');
            $table->time('horaCierre');
            $table->timestamps();

            $table->foreign('idDia')->references('id')->on('days');
            $table->foreign('idLoteria')->references('id')->on('lotteries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropForeign(['idDia']);
        $table->dropForeign(['idLoteria']);
        Schema::dropIfExists('day_lottery');
    }
}
