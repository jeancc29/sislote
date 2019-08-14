<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdventatemporalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('idventatemporals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idBanca');
            $table->bigInteger('idVenta'); //este sera el idVenta temporal
            $table->string('idVentaHash');
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
        Schema::dropIfExists('idventatemporals');
    }
}
