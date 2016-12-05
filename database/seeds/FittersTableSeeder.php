<?php

use Illuminate\Database\Seeder;

class FittersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        for($i=0; $i<=10; $i++){
            App\Fitter::create([
                'uuid'=> $faker->uuid,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->email,
                'password' => bcrypt('foregan1'),
                'phone_no' => $faker->phoneNumber,
                'address' => $faker->streetAddress,
                'city' => $faker->randomElement(['ibadan','lagos']),
                'state' => $faker -> randomElement(['Oyo','Lagos','Rivers']),
                'country' => 'Nigeria',
                'profile_image' => 'http://localhost:8000/images/si2.jpg',
            ]);
        }
    }
}
