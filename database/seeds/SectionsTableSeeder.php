<?php

use Illuminate\Database\Seeder;

class SectionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('sections')->insert([
			'name' => 'Wstęp',
			'chapter_id' => 1
		]);
    }
}
