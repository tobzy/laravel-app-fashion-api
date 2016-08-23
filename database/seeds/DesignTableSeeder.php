<?php

use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class DesignTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $time = new \Carbon\Carbon();
        $time = $time->timestamp;
        $design = new \App\Design();
        $design->title = "Center Table";
        $design->description = "Very Smooth, Lovely";
        $design->uuid = Uuid::uuid1()."_".$time;
        $design->user_id = 1;
        $design->original_name = "food.jpg";
        $design->location = 'uploads/30.jpg';
        $design->save();

        $time = new \Carbon\Carbon();
        $time = $time->timestamp;
        $design = new \App\Design();
        $design->title = "Media Cabinet";
        $design->description = "Very Smooth, Lovely";
        $design->uuid = Uuid::uuid1()."_".$time;
        $design->user_id = 1;
        $design->original_name = "meat.jpg";
        $design->location = 'uploads/32.jpg';
        $design->save();

        $time = new \Carbon\Carbon();
        $time = $time->timestamp;
        $design = new \App\Design();
        $design->title = "Entertainment Unit";
        $design->description = "Very Smooth, Lovely";
        $design->uuid = Uuid::uuid1()."_".$time;
        $design->user_id = 1;
        $design->original_name = "love.jpg";
        $design->location = 'uploads/31.jpg';
        $design->save();
    }
}
