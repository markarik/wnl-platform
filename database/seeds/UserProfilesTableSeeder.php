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
        foreach (UserSeeder::USERS as $user) {
            $userId = \DB::table('users')->select(['id'])->where('email', $user['email'])->first()->id;
            \DB::table('user_profiles')->insert([
                'user_id'      => $userId,
                'first_name'   => $user['first_name'],
                'last_name'    => $user['last_name'],
                'public_email' => $user['email'],
                'public_phone' => null,
                'username'     => $user['email'],
                'avatar'       => null,
            ]);
	    }
	}
}
