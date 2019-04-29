<?php


namespace App\Observers;


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
		if($qnaAnswer->user->isModerator()){
			$qnaAnswer->verified_at = now();
		}
	}
}
