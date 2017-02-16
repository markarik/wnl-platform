<?php

use Illuminate\Database\Seeder;

class SlidesTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('slides')->insert([
			'content' => '<strong>Example Slide</strong>',
		]);
	}
}
