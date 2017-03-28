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
			'name'     => 'Kurs internetowy + warsztaty',
			'price'    => 2200.00,
			'slug'     => 'wnl-online-onsite',
			'quantity' => 198,
		]);

		DB::table('products')->insert([
			'name'     => 'Kurs internetowy',
			'price'    => 1500.00,
			'slug'     => 'wnl-online',
			'quantity' => 97,
		]);
	}
}
