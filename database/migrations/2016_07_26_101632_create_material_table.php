<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materials',function(Blueprint $table){
            $table -> increments('id');
            $table -> integer('material_category_id');
            $table -> enum('grade',['low','medium','high']);
            $table -> string('image');
            $table -> string('color');
            $table -> integer('available_qty');
            $table -> float('price')->comment('the pricing is per 5 yards');
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
        Schema::drop('materials');
    }
}
