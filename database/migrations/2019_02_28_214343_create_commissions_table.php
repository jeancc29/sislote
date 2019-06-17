<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idBanca');
            $table->unsignedInteger('idLoteria');
            //$table->unsignedInteger('idSorteo');
            //$table->decimal('monto', 20, 2);

            $table->decimal('directo', 10, 2)->default(0);
            $table->decimal('pale', 10, 2)->default(0);
            $table->decimal('tripleta', 10, 2)->default(0);
            $table->decimal('superPale', 10, 2)->default(0);
           

            $table->timestamps();

            $table->foreign('idBanca')->references('id')->on('branches');
            $table->foreign('idLoteria')->references('id')->on('lotteries');
            //$table->foreign('idSorteo')->references('id')->on('draws');
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
        //$table->dropForeign(['idSorteo']);
        Schema::dropIfExists('commissions');
    }
}
