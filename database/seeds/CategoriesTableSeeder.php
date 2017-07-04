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

		DB::table('categories')->insert([
			['name' => 'Kardiologia', 'parent_id' => App\Models\Category::where('name', 'Interna')->first()->id],
			['name' => 'Pulmonologia', 'parent_id' => App\Models\Category::where('name', 'Interna')->first()->id],
			['name' => 'Gastroenterologia', 'parent_id' => App\Models\Category::where('name', 'Interna')->first()->id],
			['name' => 'Endokrynologia', 'parent_id' => App\Models\Category::where('name', 'Interna')->first()->id],
			['name' => 'Hematologia', 'parent_id' => App\Models\Category::where('name', 'Interna')->first()->id],
			['name' => 'Nefrologia', 'parent_id' => App\Models\Category::where('name', 'Interna')->first()->id],
			['name' => 'Reumatologia', 'parent_id' => App\Models\Category::where('name', 'Interna')->first()->id],
			['name' => 'Diabetologia', 'parent_id' => App\Models\Category::where('name', 'Interna')->first()->id],
			['name' => 'Laryngologia', 'parent_id' => App\Models\Category::where('name', 'Interna')->first()->id],
		]);
	}
}
