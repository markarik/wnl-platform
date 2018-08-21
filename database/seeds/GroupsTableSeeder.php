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
				'course_id'     => 1,
				'order_number'  => 1001,
			],
		]);
	}
}
