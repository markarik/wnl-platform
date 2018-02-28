<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PermissionsSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$now = Carbon::now();

		DB::table('permissions')->insert([
			[
				'slug'        => 'access_lesson',
				'name'        => 'Lesson access',
				'description' => 'Required to access a lesson.',
				'created_at'  => $now,
				'updated_at'  => $now,
			],
			[
				'slug'        => 'access_moderators_feed',
				'name'        => 'Access moderators feed',
				'description' => 'Required to access moderators feed and moderators chat room.',
				'created_at'  => $now,
				'updated_at'  => $now,
			],
		]);
	}
}
