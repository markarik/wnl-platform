<?php

use Illuminate\Database\Seeder;

class CommentsSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('comments')->insert([
			[
				'text'    => 'Cześć! Jak widzicie, każde pytanie będzie można skomentować!',
				'user_id' => 4,
			],
			[
				'text'    => 'Świetnie! Czy to znaczy, że będziemy mogli łatwo dyskutować o odpowiedziach i błędach w pytaniach?',
				'user_id' => 5,
			],
			[
				'text'    => 'Dokładnie tak! :D',
				'user_id' => 4,
			],
		]);

		DB::table('commentables')->insert([
			[
				'comment_id'       => 1,
				'commentable_id'   => 1,
				'commentable_type' => 'App\Models\QnaQuestions',
			],
		]);
	}
}
