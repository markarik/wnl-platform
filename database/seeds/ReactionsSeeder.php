<?php

use Illuminate\Database\Seeder;

class ReactionsSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('reactions')->insert([
			['type' => 'upvote'],
			['type' => 'downvote'],
			['type' => 'thanks'],
			['type' => 'bookmark'],
			['type' => 'watch'],
		]);
	}
}
