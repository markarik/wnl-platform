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
			'type'      => 'end',
			'content'   => DatabaseSeeder::file('demo/end.html'),
			'name'      => 'Następna lekcja',
			'lesson_id' => 1,
		]);
		// Lesson 2
		DB::table('screens')->insert([
			'type'      => 'html',
			'content'   => DatabaseSeeder::file('demo/foundations.html'),
			'name'      => 'Wstęp',
			'lesson_id' => 2,
		]);
		DB::table('screens')->insert([
			'type'      => 'end',
			'content'   => DatabaseSeeder::file('demo/end2.html'),
			'name'      => 'Następna lekcja',
			'lesson_id' => 2,
		]);
		// Lesson 3
		DB::table('screens')->insert([
			'type'      => 'html',
			'content'   => DatabaseSeeder::file('demo/example.html'),
			'name'      => 'Wstęp',
			'lesson_id' => 3,
		]);
		DB::table('screens')->insert([
			'type'      => 'html',
			'content'   => DatabaseSeeder::file('demo/repetitions.html'),
			'name'      => 'Powtórki',
			'lesson_id' => 3,
		]);
		DB::table('screens')->insert([
			'type'      => 'quiz',
			'name'      => 'Pytania kontrolne',
			'lesson_id' => 3,
			'meta'      => json_encode([
				'resources' => [
					[
						'name' => config('papi.resources.quiz-sets'),
						'id'   => 1,
					],
				],
			]),
		]);
		DB::table('screens')->insert([
			'type'      => 'end',
			'content'   => DatabaseSeeder::file('demo/end3.html'),
			'name'      => 'Zakończenie lekcji',
			'lesson_id' => 3,
		]);
	}
}
