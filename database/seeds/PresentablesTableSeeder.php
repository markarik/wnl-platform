<?php

use Illuminate\Database\Seeder;

class PresentablesTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('presentables')->insert([
			'slide_id'          => 1,
			'presentable_id'    => 1,
			'presentable_model' => 'App\Models\Subject',
		]);

		DB::table('presentables')->insert([
			'slide_id'          => 1,
			'presentable_id'    => 1,
			'presentable_model' => 'App\Models\Snippet',
		]);
	}
}
