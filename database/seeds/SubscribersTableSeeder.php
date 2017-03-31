<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SubscribersTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$bulk = [];
		$emails = Storage::get('subscribers.txt');
		foreach (explode("\n", $emails) as $email) {
			$bulk[] = ['email' => $email];
		}

		DB::table('subscribers')->insert($bulk);
	}
}
