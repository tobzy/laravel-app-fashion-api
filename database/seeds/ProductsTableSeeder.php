<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Ramsey\Uuid\Uuid;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $faker = Faker::create();

        foreach(App\Material::all() as $material){
            $count = $faker -> numberBetween(0,6);

            for($i = 0; $i<= $count; $i++){
                App\Product::create([
                    'product_name' => $material->color.' '.$material->category->name,
                    'uuid' => Uuid::uuid1(),
                    'price' => $faker -> randomFloat($nbMaxDecimal = 2,$min = 2500,$max = 10000),
                    'description' => $faker -> sentence($nbWords = 8),
                    'category' => $faker -> randomElement(['men','women']),
                    'type' => $faker -> randomElement(['clothe']),
                    'image' => 'http://localhost:8000/images/7.jpg',
                    'default_material' => $faker -> numberBetween(1,10)
                ]);
            }
        }
    }
}
