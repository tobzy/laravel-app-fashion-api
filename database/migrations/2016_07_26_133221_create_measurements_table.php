<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeasurementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('measurements',function(Blueprint $table){
            $table -> increments('id');
            $table -> integer('user_id');
            $table -> float('arm');
            $table -> float('waist');
            $table -> float('burst');
            $table -> float('leg');
            $table -> float('neck');
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
        Schema::drop('measurements');
    }
}
