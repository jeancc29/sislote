<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketswinnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticketswinners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('idTicket');
            $table->timestamps();

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
        $table->dropForeign(['idTicket']);
        Schema::dropIfExists('ticketswinners');
    }
}
