<?php

use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->insert([
            'name'  => 'Więcej niż LEK (Kurs internetowy + warsztaty)',
            'price' => 2000.00,
        ]);

        DB::table('products')->insert([
            'name'  => 'Więcej niż LEK (Kurs internetowy)',
            'price' => 1.00,
        ]);
    }
}
