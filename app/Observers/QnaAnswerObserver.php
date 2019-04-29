<?php


namespace App\Observers;


use App\Models\Page;
use App\Models\QnaAnswer;
use App\Jobs\DeleteModels;
use App\Jobs\DetachReactions;
use Illuminate\Foundation\Bus\DispatchesJobs;

class QnaAnswerObserver
{
	use DispatchesJobs;

	public function deleting(QnaAnswer $qnaAnswer)
	{
		$this->dispatch(new DeleteModels($qnaAnswer->comments));
		$this->dispatch(new DetachReactions($qnaAnswer));
	}

	public function creating(QnaAnswer $qnaAnswer)
	{
		$excludedDiscussions = Page::whereNotNull('discussion_id')->pluck('discussion_id')->toArray();
		$excluded = in_array($qnaAnswer->question->discussion_id, $excludedDiscussions);

		if($qnaAnswer->user->isModerator() || !$excluded){
			$qnaAnswer->verified_at = now();
		}
	}
}
