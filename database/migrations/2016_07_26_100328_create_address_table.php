<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses',function(Blueprint $table){
            $table -> increments('id');
            $table ->integer('user_id');
            $table -> string('street_add');
            $table -> string('city');
            $table -> string('state');
            $table -> string('country');
            $table -> enum('type',['Home','Work','Public Area']);
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
        Schema::drop('addresses');
    }
}
