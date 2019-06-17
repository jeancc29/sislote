<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayscombinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payscombinations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idLoteria');
            $table->unsignedInteger('idBanca');
            $table->decimal('primera', 10, 2)->default(0);
            $table->decimal('segunda', 10, 2)->default(0);
            $table->decimal('tercera', 10, 2)->default(0);
            $table->decimal('primeraSegunda', 10, 2)->default(0);
            $table->decimal('primeraTercera', 10, 2)->default(0);
            $table->decimal('segundaTercera', 10, 2)->default(0);
            $table->decimal('tresNumeros', 10, 2)->default(0);
            $table->decimal('dosNumeros', 10, 2)->default(0);
            $table->decimal('primerPago', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('idLoteria')->references('id')->on('lotteries');
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
        $table->dropForeign(['idLoteria', 'idBanca']);
        Schema::dropIfExists('payscombinations');
    }
}
