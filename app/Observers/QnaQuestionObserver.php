<?php namespace App\Observers;

use App\Events;
use App\Models\QnaQuestion;
use Illuminate\Foundation\Bus\DispatchesJobs;

class QnaQuestionObserver
{
	use DispatchesJobs;


	public function created(QnaQuestion $qnaQuestion)
	{
		//
	}

}
