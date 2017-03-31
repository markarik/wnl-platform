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
		// Lesson 1
		DB::table('screens')->insert([
			'type'      => 'html',
			'content'   => Storage::get('demo/mission.html'),
			'name'      => 'Wstęp',
			'lesson_id' => 1,
		]);
		DB::table('screens')->insert([
			'type'      => 'end',
			'content'   => Storage::get('demo/end.html'),
			'name'      => 'Koniec lekcji',
			'lesson_id' => 1,
		]);

		// Lesson 2
		DB::table('screens')->insert([
			'type'      => 'html',
			'content'   => Storage::get('demo/foundations.html'),
			'name'      => 'Wstęp',
			'lesson_id' => 2,
		]);
		DB::table('screens')->insert([
			'type'      => 'end',
			'content'   => Storage::get('demo/end.html'),
			'name'      => 'Koniec lekcji',
			'lesson_id' => 2,
		]);

		// Lesson 3
		DB::table('screens')->insert([
			'type'      => 'html',
			'content'   => Storage::get('demo/example.html'),
			'name'      => 'Wstęp',
			'lesson_id' => 3,
		]);
		DB::table('screens')->insert([
			'type'      => 'app',
			'content'   => Storage::get('demo/questions.html'),
			'name'      => 'Pytania kontrolne',
			'lesson_id' => 3,
		]);
		DB::table('screens')->insert([
			'type'      => 'end',
			'content'   => Storage::get('demo/end.html'),
			'name'      => 'Koniec lekcji',
			'lesson_id' => 3,
		]);
	}
}
