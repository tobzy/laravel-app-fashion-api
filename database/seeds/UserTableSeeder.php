<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Ramsey\Uuid\Uuid;

class UserTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // create a main user for the development process that we are in control of
        $user = App\User::create([
            'uuid' => Uuid::uuid1(),
            'first_name' => 'Amadosi',
            'last_name' => 'Odaibo',
            'telephone' => '08082315489',
            'email' => 'odaiboamadosi@gmail.com',
            'password' => bcrypt('foregan1'),
            'confirmation' => 1,
            'designer' => 1,
        ]);
        App\Address::create([
            'user_id' => $user->id,
            'street_add' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => 'ibadan',
            'type' => 'Home',
            'country' => $faker->country,
            'phone_no' => $faker->phoneNumber
        ]);
        App\Address::create([
            'user_id' => $user->id,
            'street_add' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => 'ibadan',
            'type' => 'Work',
            'country' => $faker->country,
            'phone_no' => $faker->phoneNumber
        ]);

        App\Measurement::create([
            'user_id' => $user->id,
            'arm' => 23.4,
            'waist' => 32.2,
            'burst' => 30.1,
            'leg' => 34.0,
            'neck' => 16.9,
        ]);

        //create general users for the rest of the process
        for ($i = 0; $i < 20; $i++) {
            $user = App\User::create([
                'uuid' => $faker->uuid,
                'first_name' => $faker->firstName($gender = null | 'male' | 'female'),
                'last_name' => $faker->lastName,
                'telephone' => $faker->phoneNumber,
                'email' => $faker->email,
                'password' => bcrypt('foregan1'),
                'confirmation' => $faker->randomElement($array = [0, 1]),
                'designer' => $faker->randomElement($array = [0, 1]),
            ]);

            App\Address::create([
                'user_id' => $user->id,
                'street_add' => $faker->streetAddress,
                'city' => $faker->city,
                'state' => 'ibadan',
                'type' => 'Home',
                'country' => $faker->country,
                'phone_no' => $faker->phoneNumber

            ]);
            App\Address::create([
                'user_id' => $user->id,
                'street_add' => $faker->streetAddress,
                'city' => $faker->city,
                'state' => 'ibadan',
                'type' => 'Work',
                'country' => $faker->country,
                'phone_no' => $faker->phoneNumber
            ]);
            App\Measurement::create([
                'user_id' => $user->id,
                'arm' => 23.4,
                'waist' => 32.2,
                'burst' => 30.1,
                'leg' => 34.0,
                'neck' => 16.9,
            ]);
        }
    }

}
