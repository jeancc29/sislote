<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombres');
            $table->string('apellidos')->nullable();
            $table->string('sexo')->nullable();
            $table->string('email')->unique();
            $table->string('telefono')->nullable();
            $table->string('celular')->nullable();
            $table->string('usuario')->unique();
            $table->string('password');
            $table->unsignedInteger('idRole');
            $table->integer('status')->default(1);
            $table->timestamp('email_verified_at')->nullable();
            
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
