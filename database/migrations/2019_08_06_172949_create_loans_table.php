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
            $table->decimal('montoCuotas', 20, 2)->default(0);
            $table->decimal('numeroCuotas', 20, 2)->default(0);
            $table->decimal('tasaInteres', 20, 2)->default(0);
            $table->decimal('mora', 20, 2)->default(0);
            $table->integer('status')->default(1);
            $table->integer('diasGracia')->default(0);
            $table->string('detalles')->nullable();

            $table->unsignedInteger('idFrecuencia');
            $table->dateTime('fechaInicio'); //Fecha desde la cual se empezara a calcular la fecha de pago
            // $table->unsignedInteger('idDia')->nullable();
            
            
      
            $table->foreign('idFrecuencia')->references('id')->on('frecuencies');
            // $table->foreign('idDia')->references('id')->on('days');
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
        $table->dropForeign(['idFrecuencia']);
        Schema::dropIfExists('loans');
    }
}
