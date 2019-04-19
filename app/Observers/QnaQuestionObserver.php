<?php namespace App\Observers;

use App\Events;
use App\Jobs\DeleteModels;
use App\Jobs\DetachReactions;
use App\Jobs\LogResourceUpdate;
use App\Models\QnaQuestion;
use Auth;
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

	public function updated(QnaQuestion $qnaQuestion)
	{
		$this->dispatch(new LogResourceUpdate($qnaQuestion, Auth::user()->id));
	}
}
