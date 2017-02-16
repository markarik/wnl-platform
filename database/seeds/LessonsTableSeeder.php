<?php

use Illuminate\Database\Seeder;

class LessonsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('lessons')->insert([
			'name'     => 'Example lesson',
			'group_id' => 1,
		]);
	}
}
