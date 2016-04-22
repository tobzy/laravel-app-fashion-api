<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'first_name' => 'Amadosi',
            'last_name' => 'Odaibo',
            'telephone' => '08166925838',
            'address' => 'No 15 sankore avenue U.I',
            'city' => 'Ibadan',
            'state' => 'Oyo',
            'country' => 'Nigeria',
            'email' => 'odaiboamadosi@yahoo.com',
            'password' => bcrypt('foregan1'),
        ]);
        
        DB::table('users')->insert([
            'first_name' => 'Osagie',
            'last_name' => 'Omon',
            'telephone' => '08038355439',
            'address' => 'No 15 Appar Apapa',
            'city' => 'Ikeja',
            'state' => 'Lagos',
            'country' => 'Nigeria',
            'email' => 'cjaeomon@yahoo.com',
            'password' => bcrypt('foregan1'),
        ]);
    }
}
