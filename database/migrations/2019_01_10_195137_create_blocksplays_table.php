<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlocksplaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blocksplays', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idBanca');
            $table->unsignedInteger('idLoteria');
            $table->unsignedInteger('idSorteo');
            $table->string('jugada', 6);
            $table->decimal('montoInicial', 10, 2);
            $table->decimal('monto', 10, 2);
            $table->datetime('fechaDesde');
            $table->datetime('fechaHasta');
            $table->integer('idUsuario');
            $table->boolean('status');
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
        Schema::dropIfExists('blocksplays');
    }
}
