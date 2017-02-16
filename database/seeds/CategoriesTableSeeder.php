<?php

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('categories')->insert([
			['name' => 'Interna'],
			['name' => 'Pediatria'],
			['name' => 'Chirurgia'],
			['name' => 'Ginekologia'],
			['name' => 'Psychiatria'],
			['name' => 'Medycyna ratunkowa i anestezjologia'],
			['name' => 'Medycyna rodzinna'],
			['name' => 'Zdrowie publiczne, Bioetyka, Prawo medyczne'],
		]);
	}
}
