<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdmonedaToBlocksdirtygenerals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blocksdirtygenerals', function (Blueprint $table) {
            $table->unsignedInteger('idMoneda');
            $table->foreign('idMoneda')->references('id')->on('coins');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blocksdirtygenerals', function (Blueprint $table) {
            $table->dropForeign("idMoneda");
            $table->dropColumn("idMoneda");
        });
    }
}
