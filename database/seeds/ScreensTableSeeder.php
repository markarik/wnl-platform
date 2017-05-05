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
			'type'      => 'quiz',
			'name'      => 'Pytania kontrolne',
			'lesson_id' => 1,
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
			'content'   => Storage::get('demo/end.html'),
			'name'      => 'Następna lekcja',
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
			'type'      => 'widget',
			'content'   => Storage::get('demo/questions.html'),
			'name'      => 'Pytania kontrolne',
			'lesson_id' => 2,
		]);
		DB::table('screens')->insert([
			'type'      => 'end',
			'content'   => Storage::get('demo/end2.html'),
			'name'      => 'Następna lekcja',
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
			'type'      => 'widget',
			'content'   => Storage::get('demo/mission.html'),
			'name'      => 'Wykorzystanie wyobraźni',
			'lesson_id' => 3,
		]);
		DB::table('screens')->insert([
			'type'      => 'end',
			'content'   => Storage::get('demo/end3.html'),
			'name'      => 'Koniec kursu',
			'lesson_id' => 3,
		]);

		// Lesson 4
		DB::table('screens')->insert([
			'type'      => 'quiz',
			'name'      => 'Pytania kontrolne',
			'lesson_id' => 4,
			'meta'      => json_encode([
				'resources' => [
					[
						'name' => config('papi.resources.quiz-sets'),
						'id'   => 2,
					],
				],
			]),
		]);
		DB::table('screens')->insert([
			'type'      => 'widget',
			'content'   => Storage::get('demo/reumatologia2.html'),
			'name'      => 'Powtórki',
			'lesson_id' => 4,
		]);
		DB::table('screens')->insert([
			'type'      => 'end',
			'content'   => Storage::get('demo/reumatologia.html'),
			'name'      => 'Następna lekcja',
			'lesson_id' => 4,
		]);
	}
}
