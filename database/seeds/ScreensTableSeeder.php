<?php

use Illuminate\Database\Seeder;

class ScreensTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('screens')->insert([
			'structure_id'  => 1,
			'snippet_id' => 1,
		]);
	}
}
