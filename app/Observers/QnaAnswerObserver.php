<?php


namespace App\Observers;


use App\Models\QnaAnswer;
use App\Jobs\DeleteModels;
use Illuminate\Foundation\Bus\DispatchesJobs;

class QnaAnswerObserver
{
	use DispatchesJobs;

	public function deleting(QnaAnswer $qnaAnswer)
	{
		$this->dispatch(new DeleteModels($qnaAnswer->comments));
	}

	public function created(QnaAnswer $qnaAnswer)
	{
		//
	}

}
