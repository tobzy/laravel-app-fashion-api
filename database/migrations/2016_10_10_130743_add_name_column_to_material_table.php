<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNameColumnToMaterialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('materials',function(Blueprint $table){
            //todo remove the default method call
            $table->string('name')->default('Blue Daviva Atiku');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('materials',function(Blueprint $table){
            //todo remove the default method call
            $table->dropColumn('name');
        });
    }
}
