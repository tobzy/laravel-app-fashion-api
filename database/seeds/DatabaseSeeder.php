<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //todo uncomment this line
         //$this->call(DesignerTableSeeder::class);
        $tables = [
            'users',
            'products',
            'materials',
            'addresses',
            'orders',
            'order_contents',
            'sessions',
            'fitters',
            'measurements',
            'product_images'
        ];

        foreach($tables as $table){
            DB::table($table) -> truncate();
        }

         $this->call(UserTableSeeder::class);
        $this->call(MaterialsTableSeeder::class);
         $this->call(ProductsTableSeeder::class);
        $this->call(FittersTableSeeder::class);

    }
}
