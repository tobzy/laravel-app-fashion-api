<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductAndMaterialPriceToOrdercontent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_contents',function (Blueprint $table){
            $table -> dropColumn('price_total');
            $table -> float('product_price');
            $table -> float('material_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_contents',function (Blueprint $table){

            $table -> float('price_total');
            $table -> dropColumn('product_price');
            $table -> dropColumn('material_price');
        });
    }
}
