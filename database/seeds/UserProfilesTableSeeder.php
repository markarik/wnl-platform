<?php

use Illuminate\Database\Seeder;

class UserProfilesTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('user_profiles')->insert([
			'user_id'      => 1,
			'first_name'   => 'Kuba',
			'last_name'    => 'Karmiński',
			'public_email' => 'jlkarminski@gmail.com',
			'public_phone' => null,
			'username'     => 'kuba',
			'avatar'       => null,
		]);

		DB::table('user_profiles')->insert([
			'user_id'      => 2,
			'first_name'   => 'Adam',
			'last_name'    => 'Karmiński',
			'public_email' => 'adamkarminski@gmail.com',
			'public_phone' => null,
			'username'     => 'troophel',
			'avatar'       => null,
		]);

		DB::table('user_profiles')->insert([
			'user_id'      => 3,
			'first_name'   => 'Prezes',
			'last_name'    => 'Chrupek',
			'public_email' => 'prezeschrupek@bethink.pl',
			'public_phone' => null,
			'username'     => 'chrupek',
			'avatar'       => null,
		]);
	}
}
