<?php

use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;
class DesignerTableSeeder extends Seeder
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
        $designer = new \App\Designer();
        $designer->username = "dosi";
        $designer->full_name = "Osagie Amadosi";
        $designer->uuid = Uuid::uuid1()."_".$time;
        $designer->email = 'dosi@gmail.com';
        $designer->password = bcrypt('meatmeat');
        $designer->save();
    }
}
