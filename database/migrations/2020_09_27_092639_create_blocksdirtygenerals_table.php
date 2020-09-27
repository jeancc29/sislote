<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlocksdirtygeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blocksdirtygenerals', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->unsignedInteger('idLoteria');
            $table->unsignedInteger('idSorteo');
            $table->integer('cantidad');
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
        $table->dropForeign(['idLoteria']);
        $table->dropForeign(['idSorteo']);
        Schema::dropIfExists('blocksdirtygenerals');
    }
}
