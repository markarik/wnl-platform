<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    const ROLES = [
        'admin',
        'moderator',
        'workshop-participant',
    ];

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		\DB::table('roles')->insert(
            array_map(function($roleName) {
                return ['name' => $roleName];
            }, self::ROLES)
        );
	}
}
