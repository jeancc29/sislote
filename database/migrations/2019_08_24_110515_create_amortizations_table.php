<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmortizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amortizations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idPrestamo');
            $table->date('fecha');
            $table->decimal('numeroCuota', 20, 2)->default(0);
            $table->decimal('montoCuota', 20, 2)->default(0);
            $table->decimal('montoInteres', 20, 2)->default(0);
            $table->decimal('amortizacion', 20, 2)->default(0);
            $table->decimal('montoPagadoCapital', 20, 2)->default(0);
            $table->decimal('montoPagadoInteres', 20, 2)->default(0);

            $table->timestamps();

            $table->foreign('idPrestamo')->references('id')->on('loans');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropForeign(['idPrestamo']);
        Schema::dropIfExists('amortizations');
    }
}
