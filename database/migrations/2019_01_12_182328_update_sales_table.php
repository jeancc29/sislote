<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->foreign('idUsuario')->references('id')->on('users');
            $table->foreign('idBanca')->references('id')->on('branches');
            $table->foreign('idTicket')->references('id')->on('tickets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropForeign(['idUsuario']);
        $table->dropForeign(['idBanca']);
        $table->dropForeign(['idTicket']);
    }
}
