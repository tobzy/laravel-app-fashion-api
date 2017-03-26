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
                $cat = $faker -> randomElement(['men','women']);
                $product = App\Product::create([
                    'product_name' => $material->color.' '.$material->category->name,
                    'uuid' => Uuid::uuid1(),
                    'price' => $faker -> randomFloat($nbMaxDecimal = 2,$min = 2500,$max = 10000),
                    'description' => $faker -> sentence($nbWords = 8),
                    'category' => $cat,
                    'type' => $faker -> randomElement(['cloth']),
                    'image' => $cat=='men' ? $faker -> randomElement(['nattivv.jpg','nattivv2.jpg']) : $faker -> randomElement(['nattivv3.jpg','nattivv4.png']),
                    'default_material' => $faker -> numberBetween(1,10)
                ]);

                // insert images for the the product
                for ($j=0; $j<3; $j++){
                    App\ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $cat=='men' ? $faker -> randomElement(['nattivv.jpg','nattivv2.jpg']) : $faker -> randomElement(['nattivv3.jpg','nattivv4.png']),
                        'alt_text' => 'image for '.$product->product_name
                    ]);
                }

            }
        }
    }
}
