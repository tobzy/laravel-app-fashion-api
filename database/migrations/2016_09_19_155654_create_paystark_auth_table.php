<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaystarkAuthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_stack_authorisation',function (Blueprint $table){
            $table-> increments('id');
            $table -> integer('user_id');
            $table -> integer('customer_id');
            $table -> string('customer_code');
            $table -> string('auth_code');
            $table -> string('card_type');
            $table -> string('last4');
            $table -> string('exp_month');
            $table ->string('exp_year');
            $table ->string('channel');
            $table ->boolean('reusable');
            $table ->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pay_stack_authorisation');
    }
}
