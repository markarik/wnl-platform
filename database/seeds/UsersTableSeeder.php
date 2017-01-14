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
            'first_name' => 'Kuba',
            'last_name'  => 'Karmiński',
            'email'      => 'jlkarminski@gmail.com',
            'password'   => bcrypt('secret'),
        ]);

        DB::table('users')->insert([
            'first_name' => 'Adam',
            'last_name'  => 'Karmiński',
            'email'      => 'adamkarminski@gmail.com',
            'password'   => bcrypt('secret'),
        ]);
    }
}
