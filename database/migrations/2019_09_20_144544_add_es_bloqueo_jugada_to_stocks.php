<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEsBloqueoJugadaToStocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->boolean('esBloqueoJugada')->default(0); 
            // Cuando se crea o actualiza un bloqueo loteria tambien se actualizan los bloqueos de la tabla
            // stock pero solamente se actualizaran los bloqueos de la tabla stocks que no sean bloqueos de jugadas blocksplays
            // entonces este campo me permitira saber si el bloque es jugada o no
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('esBloqueoJugada');
        });
    }
}
