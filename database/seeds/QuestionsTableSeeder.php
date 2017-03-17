<?php

use Illuminate\Database\Seeder;

class QuestionsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('questions')->insert([
			[
				'text'    => 'Halo Halo?',
				'user_id' => 1,
			],
			[
				'text'    => 'Co to k*** jest azotan?',
				'user_id' => 2,
			],
			[
				'text'    => 'Jak zap***** praca?',
				'user_id' => 1,
			],
		]);
	}
}
