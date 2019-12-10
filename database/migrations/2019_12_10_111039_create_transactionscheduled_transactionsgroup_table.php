<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionscheduledTransactionsgroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactionscheduled_transactionsgroup', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idTransaccion');
            $table->unsignedInteger('idGrupo');
            $table->timestamps();

            $table->foreign('idTransaccion')->references('id')->on('transactionscheduleds');
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
        Schema::dropIfExists('transactionscheduled_transactionsgroup');
    }
}
