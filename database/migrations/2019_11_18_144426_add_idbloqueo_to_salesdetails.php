<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdbloqueoToSalesdetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salesdetails', function (Blueprint $table) {
            $table->unsignedInteger('idBloqueo');     
            $table->foreign('idBloqueo')->references('id')->on('stocks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salesdetails', function (Blueprint $table) {
            $table->dropForeign(['idBloqueo']);   
            $table->dropColumn('idBloqueo');
        });
    }
}
