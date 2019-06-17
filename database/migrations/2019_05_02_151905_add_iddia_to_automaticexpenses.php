<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIddiaToAutomaticexpenses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('automaticexpenses', function (Blueprint $table) {
            $table->unsignedInteger('idDia')->nullable();
            $table->foreign('idDia')->references('id')->on('days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('automaticexpenses', function (Blueprint $table) {
            $table->dropForeign(['idDia']);
            $table->dropColumn('idDia');
        });
    }
}
