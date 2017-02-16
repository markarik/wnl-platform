<?php

use Illuminate\Database\Seeder;

class SectionsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('sections')->insert([
			'name'      => 'Example section',
			'snippet_id' => 1,
		]);
	}
}
