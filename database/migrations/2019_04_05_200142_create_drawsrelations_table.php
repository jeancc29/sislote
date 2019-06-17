<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrawsrelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drawsrelations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idLoteriaPertenece');
            $table->unsignedInteger('idSorteo');
            $table->unsignedInteger('idLoteria');
            $table->timestamps();

            $table->foreign('idSorteo')->references('id')->on('draws');
            $table->foreign('idLoteriaPertenece')->references('id')->on('lotteries');
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
        $table->dropForeign(['idSorteo', 'idLoteriaPertenece']);
        $table->dropForeign(['idLoteria']);
        Schema::dropIfExists('drawsrelations');
    }
}
