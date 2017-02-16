<?php

use Illuminate\Database\Seeder;

class StructuresTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('structures')->insert([
			['name' => 'Example'],
			['name' => 'Interna'],
			['name' => 'Pediatria'],
			['name' => 'Chirurgia'],
			['name' => 'Ginekologia'],
			['name' => 'Psychiatria'],
			['name' => 'Medycyna ratunkowa i anestezjologia'],
			['name' => 'Medycyna rodzinna'],
			['name' => 'Zdrowie publiczne, Bioetyka, Prawo medyczne'],
		]);

		DB::table('structures')->insert([
			['name' => 'Example lesson', 'parent_id' => 1],
		]);
	}
}
