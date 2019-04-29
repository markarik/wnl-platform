<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Comment;
use App\Models\QnaAnswer;
use App\Models\Page;


class MarkContentVerified extends Migration
{
	// TODO: Update users list when it's ready!
	const USERS_TO_MARK = [374];

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$now = now();
		$excludedDiscussions = Page::whereNotNull('discussion_id')->pluck('discussion_id')->toArray();
		$excludedAnswers = QnaAnswer::whereIn('user_id', self::USERS_TO_MARK)
			->whereDoesntHave('question', function ($query) use ($excludedDiscussions) {
				$query->whereNotIn('discussion_id', $excludedDiscussions);
			})->pluck('id')->toArray();

		QnaAnswer::whereIn('user_id', self::USERS_TO_MARK)
			->whereNotIn('id', $excludedAnswers)
			->update(['verified_at' => $now]);

		Comment::whereIn('user_id', self::USERS_TO_MARK)
			->where(function ($query) use ($excludedAnswers) {
				$query
					->where('commentable_type', QnaAnswer::class)
					->whereNotIn('commentable_id', $excludedAnswers);
			})
			->orWhere('commentable_type', '<>', QnaAnswer::class)
			->update(['verified_at' => $now]);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		QnaAnswer::whereIn('user_id', self::USERS_TO_MARK)->update(['verified_at' => NULL]);
		Comment::whereIn('user_id', self::USERS_TO_MARK)->update(['verified_at' => NULL]);
	}
}
