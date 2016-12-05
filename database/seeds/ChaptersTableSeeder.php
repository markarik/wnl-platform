<?php

use Illuminate\Database\Seeder;

class ChaptersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('chapters')->insert([
			'name' => 'Jelito grube',
			'module_id' => 1
		]);
    }
}
