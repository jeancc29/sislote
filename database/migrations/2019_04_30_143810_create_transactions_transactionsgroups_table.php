<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTransactionsgroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions_transactionsgroups', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idTransaccion');
            $table->unsignedInteger('idGrupo');
            $table->timestamps();

            $table->foreign('idTransaccion')->references('id')->on('transactions');
            $table->foreign('idGrupo')->references('id')->on('transactionsgroups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropForeign(['idTransaccion', 'idGrupo']);
        Schema::dropIfExists('transactions_transactionsgroups');
    }
}
