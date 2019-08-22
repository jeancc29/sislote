<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idUsuario');
            $table->unsignedInteger('idTipoEntidadPrestamo'); //Tipo de entidad, osea, si es una banca, sisteama, entidad, etc..
            $table->unsignedInteger('idTipoEntidadFondo');
            $table->unsignedInteger('idEntidadPrestamo');
            $table->unsignedInteger('idEntidadFondo');
            $table->decimal('montoPrestado', 20, 2)->default(0);
            $table->decimal('montoCuota', 20, 2)->default(0);
            $table->decimal('montoCuota', 20, 2)->default(0);

            $table->unsignedInteger('idFrecuencia');
            $table->unsignedInteger('idDia')->nullable();
            
      
            $table->foreign('idFrecuencia')->references('id')->on('frecuencies');
            $table->foreign('idDia')->references('id')->on('days');
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
        Schema::dropIfExists('loans');
    }
}
