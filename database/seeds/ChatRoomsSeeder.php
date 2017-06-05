<?php

use Illuminate\Database\Seeder;

class ChatRoomsSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$now = \Carbon\Carbon::now();

		DB::table('chat_rooms')->insert([
			[
				'name'          => 'courses-1',
				'created_at'       => $now,
				'updated_at'       => $now,
			],
			[
				'name'             => 'courses-2',
				'created_at'       => $now,
				'updated_at'       => $now,
			],
			[
				'name'             => 'private-room',
				'created_at'       => $now,
				'updated_at'       => $now,
			]
		]);
	}
}
