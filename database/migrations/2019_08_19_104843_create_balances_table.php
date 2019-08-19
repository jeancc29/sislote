<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balances', function (Blueprint $table) {
            //Esta migracion no la utilizo
            $table->increments('id');
            $table->unsignedInteger('idTipoEntidad');
            $table->unsignedInteger('idEntidad');
            $table->decimal('balance', 20, 2)->default(0);
            $table->timestamps();

            $table->foreign('idTipoEntidad')->references('id')->on('types');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropForeign(['idTipoEntidad']);
        Schema::dropIfExists('balances');
    }
}
