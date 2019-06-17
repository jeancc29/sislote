<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('idLoteria');
            $table->integer('idSorteo');
            $table->string('jugada', 6);
            $table->decimal('montoInicial', 10, 2);
            $table->decimal('monto', 10, 2);
            $table->timestamps();

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
        $table->dropForeign(['idLoteria']);
        Schema::dropIfExists('stocks');
    }
}
