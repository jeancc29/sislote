<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('idUsuario');
            $table->unsignedInteger('idBanca');
            $table->decimal('total', 20, 2);
            $table->decimal('descuentoPorcentaje', 4, 2)->default(0);
            $table->decimal('descuentoMonto', 20, 2)->default(0);
            $table->boolean('hayDescuento')->default(0);
            $table->decimal('subTotal', 20, 2);
            $table->integer('idLoteria')->nullable();
            $table->unsignedBigInteger('idTicket');
            $table->integer('status')->default(1);
            
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
        Schema::dropIfExists('sales');
    }
}
