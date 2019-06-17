<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesdetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salesdetails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('idVenta');
            $table->unsignedInteger('idLoteria');
            $table->unsignedInteger('idSorteo');
            $table->string('jugada', 6);
            $table->decimal('monto', 20, 2);
            $table->decimal('premio', 20, 2);
            $table->integer('status')->default(0);
            $table->timestamps();

            $table->foreign('idLoteria')->references('id')->on('lotteries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropForeign(['idLoteria']);
        Schema::dropIfExists('salesdetails');
    }
}
