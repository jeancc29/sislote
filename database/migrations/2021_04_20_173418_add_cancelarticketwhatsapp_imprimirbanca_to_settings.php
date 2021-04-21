<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCancelarticketwhatsappImprimirbancaToSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->unsignedInteger('cancelarTicketWhatsapp')->default(0);
            $table->unsignedInteger('imprimirNombreBanca')->default(1);
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
            $table->dropColumn("cancelarTicketWhatsapp");
            $table->dropColumn("imprimirNombreBanca");
        });
    }
}
