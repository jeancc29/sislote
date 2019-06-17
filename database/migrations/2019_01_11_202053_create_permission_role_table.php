<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission_role', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idPermiso');
            $table->unsignedInteger('idRole');
            $table->timestamps();

            $table->foreign('idPermiso')->references('id')->on('permissions');
            $table->foreign('idRole')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropForeign(['idPermiso']);
        $table->dropForeign(['idRole']);
        Schema::dropIfExists('permission_role');
    }
}
