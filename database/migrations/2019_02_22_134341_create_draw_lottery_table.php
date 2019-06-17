<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrawLotteryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('draw_lottery', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idSorteo');
            $table->unsignedInteger('idLoteria');
            $table->timestamps();

            $table->foreign('idSorteo')->references('id')->on('draws');
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
        $table->dropForeign(['idSorteo']);
        $table->dropForeign(['idLoteria']);
        Schema::dropIfExists('draw_lottery');
    }
}
