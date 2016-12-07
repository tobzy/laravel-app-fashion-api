<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFittersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fitters',function(Blueprint $table){
            $table -> integer('id');
            $table -> string('uuid');
            $table -> string('first_name');
            $table -> string('last_name');
            $table -> string('email')->unique();
            $table -> string('password');
            $table -> string('phone_no');
            $table -> string('address');
            $table -> string('city');
            $table -> string('state');
            $table -> string('country');
            $table -> string('profile_image');
            $table -> timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('fitters');
    }
}
