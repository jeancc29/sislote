<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranchesDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches_days', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idDia');
            $table->unsignedInteger('idBanca');
            $table->time('horaApertura');
            $table->time('horaCierre');
            $table->timestamps();

            $table->foreign('idDia')->references('id')->on('days');
            $table->foreign('idBanca')->references('id')->on('branches');
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
        $table->dropForeign(['idBanca']);
        Schema::dropIfExists('branches_days');
    }
}
