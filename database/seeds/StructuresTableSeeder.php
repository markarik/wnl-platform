<?php

use Illuminate\Database\Seeder;

class StructuresTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('structures')->insert([
			'name' => 'Dzień 1',
		]);
	}
}
