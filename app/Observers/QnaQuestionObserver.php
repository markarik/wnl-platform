<?php namespace App\Observers;

use App\Events;
use App\Jobs\DeleteModels;
use App\Models\QnaQuestion;
use Illuminate\Foundation\Bus\DispatchesJobs;

class QnaQuestionObserver
{
	use DispatchesJobs;

	public function deleting(QnaQuestion $qnaQuestion)
	{
		$this->dispatch(new DeleteModels($qnaQuestion->answers));
	}

	public function created(QnaQuestion $qnaQuestion)
	{
		//
	}

}
