<?php

use Illuminate\Database\Seeder;

class SectionsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('sections')->insert([
			'name'      => 'Example Section',
			'lesson_id' => 1,
			'slide_id'  => 1,
		]);
	}
}
