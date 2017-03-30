<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;


class LessonAvailabilitySeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('lesson_availabilities')->insert([
			[
				'edition_id' => 1,
				'lesson_id'  => 1,
				'start_date' => Carbon::now()->subDays(3),
			],
			[
				'edition_id' => 1,
				'lesson_id'  => 3,
				'start_date' => Carbon::now()->addDays(1),
			],
			[
				'edition_id' => 1,
				'lesson_id'  => 4,
				'start_date' => Carbon::now()->addDays(2),
			],
			[
				'edition_id' => 1,
				'lesson_id'  => 5,
				'start_date' => Carbon::now()->addDays(3),
			],
		]);
	}
}
