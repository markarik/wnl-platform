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
			[
				'name'     => 'Pulmonologia I',
				'group_id' => 1,
			],
			[
				'name'     => 'Pulmonologia II',
				'group_id' => 1,
			],
			[
				'name'     => 'Kardiologia',
				'group_id' => 1,
			],
			[
				'name'     => 'Gastroenterologia',
				'group_id' => 1,
			],
			[
				'name'     => 'Chirurgia układu pokarmowego',
				'group_id' => 2,
			],
			[
				'name'     => 'Chirurgia mózgu',
				'group_id' => 2,
			],
			[
				'name'     => 'Chirurgia ekstremalna',
				'group_id' => 2,
			],
		]);
	}
}
