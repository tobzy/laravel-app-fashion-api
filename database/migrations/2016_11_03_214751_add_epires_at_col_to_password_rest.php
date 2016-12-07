<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEpiresAtColToPasswordRest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('password_resets',function(Blueprint $blueprint){
            $blueprint -> timestamp('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('password_resets',function(Blueprint $blueprint){
            $blueprint -> dropColumn('expires_at');
        });
    }
}
