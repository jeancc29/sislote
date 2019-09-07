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
            $table->decimal('montoCapital', 20, 2)->default(0);
            $table->decimal('numeroCuotas', 20, 2)->default(0);
            $table->decimal('tasaInteres', 20, 2)->default(0);
            $table->decimal('mora', 20, 2)->default(0);
            $table->integer('status')->default(1);
            $table->integer('diasGracia')->default(0);
            $table->string('detalles')->nullable();
            //Esto me indicara cuales campos se llenaron para amortizar el prestamo y con esto
            //al momento de editar un prestamo me permitira desabilitar los campos que no fueron seleccionados
            //y habilitar los que si fueron, ejemplo... 
            //1 == montoCuota ya sea con el interes o no
            //2 == numeroCuota ya sea con el interes o no
            //3 == montoCuota y numeroCuotas para que el interes se calcule automatico
            $table->integer('idTipoAmortizacion')->default(0);

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
