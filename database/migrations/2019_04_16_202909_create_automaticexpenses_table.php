<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutomaticexpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('automaticexpenses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descripcion');
            $table->decimal('monto', 20, 2);
            // $table->datetime('fechaInicio');
           
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
        
        Schema::dropIfExists('automaticexpenses');
    }
}
