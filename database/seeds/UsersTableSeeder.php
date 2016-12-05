<?php

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
			'name' => 'Kuba Karmiński',
			'email' => 'jlkarminski@gmail.com',
			'password' => bcrypt('secret'),
		]);

		DB::table('users')->insert([
				'name' => 'Adam Karmiński',
				'email' => 'adamkarminski@gmail.com',
				'password' => bcrypt('secret'),
		]);
    }
}
