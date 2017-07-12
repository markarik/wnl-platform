<?php

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Category::firstOrCreate(['name' => 'Interna']);
		Category::firstOrCreate(['name' => 'Pediatria']);
		Category::firstOrCreate(['name' => 'Chirurgia']);
		Category::firstOrCreate(['name' => 'Ginekologia']);
		Category::firstOrCreate(['name' => 'Psychiatria']);
		Category::firstOrCreate(['name' => 'Medycyna ratunkowa i anestezjologia']);
		Category::firstOrCreate(['name' => 'Medycyna rodzinna']);
		Category::firstOrCreate(['name' => 'Zdrowie publiczne, Bioetyka, Prawo medyczne']);

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
			['name' => 'P1 Rozwój / Żywienie / Badanie', 'parent_id' => App\Models\Category::where('name', 'Pediatria')->first()->id],
			['name' => 'P2 Genetyka / Ch.Metaboliczne / Ok.Noworodkowy', 'parent_id' => App\Models\Category::where('name', 'Pediatria')->first()->id],
			['name' => 'P3 Pulmonologia', 'parent_id' => App\Models\Category::where('name', 'Pediatria')->first()->id],
			['name' => 'P4 Kardiologia', 'parent_id' => App\Models\Category::where('name', 'Pediatria')->first()->id],
			['name' => 'P5 Gastroenterologia', 'parent_id' => App\Models\Category::where('name', 'Pediatria')->first()->id],
			['name' => 'P6 Hematologia / Onkologia', 'parent_id' => App\Models\Category::where('name', 'Pediatria')->first()->id],
			['name' => 'P7 Układ Moczowy / Neurologia', 'parent_id' => App\Models\Category::where('name', 'Pediatria')->first()->id],
			['name' => 'P8 Endokrynologia / Reumatologia', 'parent_id' => App\Models\Category::where('name', 'Pediatria')->first()->id],
			['name' => 'P9 Zakaźne / Szczepienia / Alergie / Odporność', 'parent_id' => App\Models\Category::where('name', 'Pediatria')->first()->id],
			['name' => 'P10 Otolaryngologia / Stany Nagłe', 'parent_id' => App\Models\Category::where('name', 'Pediatria')->first()->id],
		]);
	}
}
