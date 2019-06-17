<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignkeyToTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('idTipo')->references('id')->on('types');
            $table->foreign('idUsuario')->references('id')->on('users');
            $table->foreign('idTipoEntidad1')->references('id')->on('types');
            $table->foreign('idTipoEntidad2')->references('id')->on('types');
            // $table->foreign('idEntidad1')->references('id')->on('branches');
            // $table->foreign('idEntidad2')->references('id')->on('entities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign
                ([
                    'idTipo', 
                    'idUsuario',
                    'idTipoEntidad1',
                    'idTipoEntidad2',
                    // 'idEntidad1',
                    // 'idEntidad2'
                ]);
        });
    }
}
