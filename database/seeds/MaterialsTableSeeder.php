<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class MaterialsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        foreach(App\MaterialCategory::all() as $category){
            $count = $faker ->numberBetween(5,10);

            for($i = 0; $i <=$count; $i++ ){

                App\Material::create([
                    'material_category_id' => $category->id,
                    'grade' => $faker ->randomElement(['low','medium','high']),
                    'image' => 'http://localhost:8000/images/si2.jpg',
                    'color' => $faker ->colorName,
                    'available_qty' => $faker -> randomDigit(),
                    'price' => $faker -> numberBetween($min = 2500, $max = 10,000)
                ]);
            }
        }
    }
}
