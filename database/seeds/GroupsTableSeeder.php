<?php

use Illuminate\Database\Seeder;

class GroupsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('groups')->insert([
			[
				'name'          => 'Przykładowa grupa lekcji',
				'course_id'     => DB::table('courses')->first()->id,
			],
		]);
	}
}
