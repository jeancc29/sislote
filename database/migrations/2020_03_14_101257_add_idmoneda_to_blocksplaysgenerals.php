<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdmonedaToBlocksplaysgenerals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blocksplaysgenerals', function (Blueprint $table) {
            $table->unsignedInteger('idMoneda')->default(1);
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
        Schema::table('blocksplaysgenerals', function (Blueprint $table) {
            $table->dropForeign(['idMoneda']);
            $table->dropColumn('idMoneda');
        });
    }
}
