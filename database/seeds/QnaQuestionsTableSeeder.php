<?php

use Illuminate\Database\Seeder;

class QnaQuestionsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$now = \Carbon\Carbon::now();
		DB::table('qna_questions')->insert([
			[
				'text'       => 'Halo Halo?',
				'user_id'    => 1,
				'created_at' => $now,
				'updated_at' => $now,
			],
			[
				'text'       => 'Co to k*** jest azotan?',
				'user_id'    => 2,
				'created_at' => $now,
				'updated_at' => $now,
			],
			[
				'text'       => 'Jak zap***** praca?',
				'user_id'    => 1,
				'created_at' => $now,
				'updated_at' => $now,
			],
		]);
	}
}
