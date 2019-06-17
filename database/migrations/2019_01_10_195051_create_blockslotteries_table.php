<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlockslotteriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blockslotteries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idBanca');
            $table->unsignedInteger('idDia');
            $table->unsignedInteger('idLoteria');
            $table->unsignedInteger('idSorteo');
            $table->decimal('monto', 10, 2);
            $table->timestamps();


            
            $table->foreign('idBanca')->references('id')->on('branches');
            $table->foreign('idDia')->references('id')->on('days');
            $table->foreign('idLoteria')->references('id')->on('lotteries');
            $table->foreign('idSorteo')->references('id')->on('draws');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropForeign(['idBanca']);
        $table->dropForeign(['idDia']);
        $table->dropForeign(['idLoteria']);
        $table->dropForeign(['idSorteo']);
        Schema::dropIfExists('blockslotteries');
    }
}
