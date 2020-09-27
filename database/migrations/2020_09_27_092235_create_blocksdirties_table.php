<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlocksdirtiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blocksdirties', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->unsignedInteger('idBanca');
            $table->unsignedInteger('idLoteria');
            $table->unsignedInteger('idSorteo');
            $table->integer('cantidad');
            
            $table->foreign('idBanca')->references('id')->on('branches');
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
        $table->dropForeign(['idLoteria']);
        $table->dropForeign(['idSorteo']);
        Schema::dropIfExists('blocksdirties');
    }
}
