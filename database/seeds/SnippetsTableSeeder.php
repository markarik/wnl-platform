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
			'type'    => 'slideshow',
		]);

		DB::table('snippets')->insert([
			'type'    => 'html',
			'content' => '<strong>siema!</strong>',
		]);

		DB::table('snippets')->insert([
			'type'    => 'app',
		]);
	}
}
