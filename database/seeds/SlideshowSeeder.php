<?php

use Illuminate\Database\Seeder;


class SlideshowSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run()
	{
		Artisan::queue('slides:import');
	}
}
