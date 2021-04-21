<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConsorsoYFormatoticketToSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('consorcio');
            $table->unsignedInteger('idTipoFormatoTicket')->nullable();
            $table->boolean('imprimirNombreConsorcio')->default(1);
            // $table->foreign('idTipoFormatoTicket')->references('id')->on('types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            // $table->dropForeign("idTipoFormatoTicket");
            $table->dropColumn("idTipoFormatoTicket");
            $table->dropColumn("idMoneda");
            $table->dropColumn("consorcio");
        });
    }
}
