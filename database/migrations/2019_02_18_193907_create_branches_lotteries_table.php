<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranchesLotteriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches_lotteries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idBanca');
            $table->unsignedInteger('idLoteria');
            $table->timestamps();

            $table->foreign('idBanca')->references('id')->on('branches');
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
        $table->dropForeign(['idBanca']);
        $table->dropForeign(['idLoteria']);
        Schema::dropIfExists('branches_lotteries');
    }
}
