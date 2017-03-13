<?php

use Illuminate\Database\Seeder;

class ScreensTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('screens')->insert([
			'type'      => 'html',
			'content'   => '<strong>siema!</strong>',
			'name'      => 'Wstęp',
			'lesson_id' => 1,
		]);

		DB::table('screens')->insert([
			'type'      => 'slideshow',
			'name'      => 'Prezentacja',
			'lesson_id' => 1,
		]);

		DB::table('screens')->insert([
			'type'      => 'app',
			'name'      => 'Pytanie kontrolne',
			'lesson_id' => 1,
		]);
	}
}
