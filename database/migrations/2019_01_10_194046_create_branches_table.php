<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        Schema::create('branches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip', 11);
            $table->string('codigo', 10);
            $table->boolean('status')->default(1);
            $table->integer('idUsuario');
            $table->string('dueno');
            $table->string('localidad');

            $table->decimal('balanceDesactivacion', 20,2);
            $table->decimal('limiteVenta', 20,2);
            $table->decimal('descontar', 10,2);
            $table->decimal('deCada', 10,2);
            $table->decimal('minutosCancelarTicket', 10,2);
            $table->string('piepagina1')->nullable();
            $table->string('piepagina2')->nullable();
            $table->string('piepagina3')->nullable();
            $table->string('piepagina4')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branches');
    }
}
