<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_contents',function(Blueprint $table){
            $table -> increments('id');
            $table -> integer('order_id');
            $table -> integer('product_id');
            $table -> integer('quantity');
            $table -> float('price_total');
            $table -> integer('material_id');
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
        Schema::drop('order_contents');
    }
}
