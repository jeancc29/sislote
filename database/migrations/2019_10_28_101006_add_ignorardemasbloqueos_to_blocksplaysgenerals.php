<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIgnorardemasbloqueosToBlocksplaysgenerals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blocksplaysgenerals', function (Blueprint $table) {
            $table->integer('ignorarDemasBloqueos')->default(0);
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
            $table->dropColumn('ignorarDemasBloqueos');
        });
    }
}
