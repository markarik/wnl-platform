<?php

use Illuminate\Database\Seeder;

class TaxonomiesSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('taxonomies')->insert([
			['name' => 'subjects'],
			['name' => 'exams'],
			['name' => 'tags'],
		]);
	}
}
