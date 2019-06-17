<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdBancaAndIdFrecuenciaToAutomaticexpenses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('automaticexpenses', function (Blueprint $table) {
            $table->unsignedInteger('idBanca');
            $table->unsignedInteger('idFrecuencia');
    

            $table->foreign('idBanca')->references('id')->on('branches');
            $table->foreign('idFrecuencia')->references('id')->on('frecuencies');
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
            $table->dropForeign(['idBanca', 'idFrecuencia']);
            $table->dropColumn('idBanca');
            $table->dropColumn('idFrecuencia');
        });
    }
}
