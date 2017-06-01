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
		$now = \Carbon\Carbon::now();

		DB::table('reactions')->insert([
			['type' => 'upvote'],
			['type' => 'downvote'],
			['type' => 'thanks'],
			['type' => 'bookmark'],
		]);

		DB::table('reactables')->insert([
			[
				'user_id'        => 1,
				'reaction_id'    => 1,
				'reactable_id'   => 1,
				'reactable_type' => 'App\Models\QnaAnswer',
				'created_at'     => $now,
				'updated_at'     => $now,
			],
		]);
	}
}
