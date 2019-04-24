<?php namespace App\Observers;

use App\Jobs\DeleteModels;
use App\Jobs\DetachReactions;
use App\Models\QnaQuestion;
use Illuminate\Foundation\Bus\DispatchesJobs;

class QnaQuestionObserver
{
	use DispatchesJobs;

	public function deleting(QnaQuestion $qnaQuestion)
	{
		$this->dispatch(new DeleteModels($qnaQuestion->qnaAnswers));
		$this->dispatch(new DetachReactions($qnaQuestion));
	}

	public function created(QnaQuestion $qnaQuestion)
	{
		//
	}
}
