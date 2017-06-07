<?php


namespace App\Observers;


use App\Models\QnaAnswer;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Events;

class QnaAnswerObserver
{
	use DispatchesJobs;


	public function created(QnaAnswer $qnaAnswer)
	{
		//
	}

}
