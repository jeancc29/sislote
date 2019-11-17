<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlocksplaysgeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blocksplaysgenerals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idLoteria');
            $table->unsignedInteger('idSorteo');
            $table->string('jugada', 6);
            $table->decimal('montoInicial', 10, 2);
            $table->decimal('monto', 10, 2);
            $table->datetime('fechaDesde');
            $table->datetime('fechaHasta');
            $table->integer('idUsuario');
            $table->boolean('status');

            $table->foreign('idLoteria')->references('id')->on('lotteries');
            $table->timestamps();
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
        Schema::dropIfExists('blocksplaysgenerals');
    }
}
