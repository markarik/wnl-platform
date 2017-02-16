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
				'name'      => 'Interna',
				'course_id' => 1,
			],
			[
				'name'      => 'Chirurgia',
				'course_id' => 1,
			],
		]);
	}
}
