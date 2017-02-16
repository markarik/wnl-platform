<?php

use Illuminate\Database\Seeder;

class SnippetsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('snippets')->insert([
			'type'    => 'html',
			'content' => '<strong>siema!</strong>',
			'name'    => 'Wstęp',
		]);

		DB::table('snippets')->insert([
			'type' => 'slideshow',
			'name' => 'Prezentacja',
		]);

		DB::table('snippets')->insert([
			'type' => 'app',
			'name' => 'Pytanie kontrolne',
		]);
	}
}
