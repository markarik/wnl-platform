<?php

use Illuminate\Database\Seeder;

class SubscribersTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('subscribers')->insert([
			['email' => 'kuba@wiecejnizlek.pl'],
		]);
	}
}
