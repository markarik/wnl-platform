<?php

use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('tags')->insert([
			['name' => 'Pomoc w nauce'],
			['name' => 'Pomoc techniczna'],
			['name' => 'Błędy'],
			['name' => 'Nowe funkcje'],
			['name' => 'Sugestie'],
			// TODO PLAT-1055 - make code independent of Warsztaty tag
			['name' => 'Warsztaty'],
		]);
	}
}
