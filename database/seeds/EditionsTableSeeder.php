<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EditionsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('editions')->insert([
			[
				'course_id'  => 1,
				'name'       => 'O kursie "Więcej niż LEK"',
				'start_date' => Carbon::parse('first day of June 2017'),
			],
		]);
	}
}
