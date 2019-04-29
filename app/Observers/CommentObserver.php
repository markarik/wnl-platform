<?php

namespace App\Observers;

use App\Models\Comment;
use App\Jobs\DeleteModels;
use App\Jobs\DetachReactions;
use App\Models\Page;
use App\Models\QnaAnswer;
use Illuminate\Foundation\Bus\DispatchesJobs;

class CommentObserver
{
	use DispatchesJobs;

	public function creating(Comment $comment)
	{
		$excludedDiscussions = Page::whereNotNull('discussion_id')->pluck('discussion_id')->toArray();
		$excludedAnswers = QnaAnswer::select()
			->whereDoesntHave('question', function ($query) use ($excludedDiscussions) {
				$query->whereNotIn('discussion_id', $excludedDiscussions);
			})->pluck('id')->toArray();
		$excluded = in_array($comment->commentable_id, $excludedAnswers);

		if($comment->user->isModerator() && !$excluded){
			$comment->verified_at = now();
		}
	}

	public function deleted(Comment $comment)
	{
		$this->dispatch(new DetachReactions($comment));
	}
}
