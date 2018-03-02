<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

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
				'slug'        => 'lesson_access',
				'name'        => 'Lesson access',
				'description' => 'Required to access a lesson.',
				'created_at'  => $now,
				'updated_at'  => $now,
			],
			[
				'slug'        => 'role_access',
				'name'        => 'Role access',
				'description' => 'Requires user to have certain role, to access a resource.',
				'created_at'  => $now,
				'updated_at'  => $now,
			],
		]);
	}
}
