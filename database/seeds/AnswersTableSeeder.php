<?php

use Illuminate\Database\Seeder;

class AnswersTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('answers')->insert([
			[
				'text'        => 'kto tam?',
				'user_id'     => 2,
				'question_id' => 1,
			],
			[
				'text'        => 'Lolol ol olo o',
				'user_id'     => 1,
				'question_id' => 1,
			],
			[
				'text'        => 'Ja',
				'user_id'     => 1,
				'question_id' => 2,
			],
			[
				'text'        => 'jak kuna rowem',
				'user_id'     => 2,
				'question_id' => 3,
			],
		]);
	}
}
