<?php

use Illuminate\Database\Seeder;

class TaggablesTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('taggables')->insert([
			[
				'tag_id'        => 1,
				'taggable_id'   => 1,
				'taggable_type' => 'App\Models\Lesson',
			],
			[
				'tag_id'        => 2,
				'taggable_id'   => 1,
				'taggable_type' => 'App\Models\Lesson',
			],
			[
				'tag_id'        => 1,
				'taggable_id'   => 1,
				'taggable_type' => 'App\Models\QnaQuestion',
			],
			[
				'tag_id'        => 2,
				'taggable_id'   => 1,
				'taggable_type' => 'App\Models\QnaQuestion',
			],
			[
				'tag_id'        => 1,
				'taggable_id'   => 2,
				'taggable_type' => 'App\Models\QnaQuestion',
			],
			[
				'tag_id'        => 2,
				'taggable_id'   => 2,
				'taggable_type' => 'App\Models\QnaQuestion',
			],
			[
				'tag_id'        => 1,
				'taggable_id'   => 3,
				'taggable_type' => 'App\Models\QnaQuestion',
			],
			[
				'tag_id'        => 2,
				'taggable_id'   => 3,
				'taggable_type' => 'App\Models\QnaQuestion',
			],
		]);
	}
}
