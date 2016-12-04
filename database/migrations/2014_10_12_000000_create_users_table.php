<?php

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
            $table -> string('uuid',60)->unique()->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('telephone')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->integer('fitter_id')->nullable();
            $table->rememberToken();
            $table->boolean('designer')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
