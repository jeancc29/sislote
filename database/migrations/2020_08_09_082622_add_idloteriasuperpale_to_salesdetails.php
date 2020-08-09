<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdloteriasuperpaleToSalesdetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salesdetails', function (Blueprint $table) {
            $table->integer("idLoteriaSuperpale")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salesdetails', function (Blueprint $table) {
            $table->dropColumn('idLoteriaSuperpale');
        });
    }
}
