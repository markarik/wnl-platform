<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('users')->insert([
			'first_name' => encrypt('Kuba'),
			'last_name'  => encrypt('Karmiński'),
			'email'      => 'jlkarminski@gmail.com',
			'phone'      => encrypt('000000000'),
			'address'    => encrypt(''),
			'password'   => bcrypt('secret'),
		]);

		DB::table('users')->insert([
			'first_name' => encrypt('Adam'),
			'last_name'  => encrypt('Karmiński'),
			'email'      => 'adamkarminski@gmail.com',
			'phone'      => encrypt('000000000'),
			'address'    => encrypt(''),
			'password'   => bcrypt('secret'),
		]);

		DB::table('users')->insert([
			'first_name' => encrypt('Prezes'),
			'last_name'  => encrypt('Chrupek'),
			'email'      => 'prezeschrupek@bethink.pl',
			'phone'      => encrypt('000000000'),
			'address'    => encrypt(''),
			'password'   => bcrypt('secret'),
		]);

		DB::table('users')->insert([
			'first_name' => encrypt('Roman'),
			'last_name'  => encrypt('Zwyczajny'),
			'email'      => 'zwyczajny@bethink.pl',
			'phone'      => encrypt('000000000'),
			'address'    => encrypt(''),
			'password'   => bcrypt('secret'),
		]);

		DB::table('users')->insert([
			'first_name' => encrypt('Robert'),
			'last_name'  => encrypt('Kardiowaskularny'),
			'email'      => 'Kardiowaskularny@bethink.pl',
			'phone'      => encrypt('000000000'),
			'address'    => encrypt(''),
			'password'   => bcrypt('secret'),
		]);

		DB::table('users')->insert([
			'first_name' => encrypt('Asia'),
			'last_name'  => encrypt('Nereczka'),
			'email'      => 'Nereczka@bethink.pl',
			'phone'      => encrypt('000000000'),
			'address'    => encrypt(''),
			'password'   => bcrypt('secret'),
		]);

		DB::table('users')->insert([
			'first_name' => encrypt('Jakub'),
			'last_name'  => encrypt('Mochol'),
			'email'      => 'jakub.mochol@wiecejnizlek.pl',
			'phone'      => encrypt('000000000'),
			'address'    => encrypt(''),
			'password'   => bcrypt('secret'),
		]);

		DB::table('role_user')->insert([
			['user_id' => 1, 'role_id' => 1],
			['user_id' => 1, 'role_id' => 3],
			['user_id' => 2, 'role_id' => 3],
			['user_id' => 2, 'role_id' => 1],
			['user_id' => 3, 'role_id' => 1],
			['user_id' => 3, 'role_id' => 2],
		]);

		DB::table('user_subscription')->insert([
			['user_id' => 1, 'access_start' => Carbon::now()->subDays(5), 'access_end' => Carbon::now()->addDays(5)],
			['user_id' => 2, 'access_start' => Carbon::now()->subDays(5), 'access_end' => Carbon::now()->addDays(5)],
			['user_id' => 3, 'access_start' => Carbon::now()->subDays(5), 'access_end' => Carbon::now()->addDays(5)],
		]);
	}
}
