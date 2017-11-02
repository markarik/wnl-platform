<?php

namespace App\Observers;

use App\Models\QuizQuestion;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\DeleteModels;
use App\Jobs\DetachReactions;

class QuizQuestionObserver
{
	use DispatchesJobs;


	public function created(QuizQuestion $quizQuestion)
	{
		//
	}

	public function deleted(QuizQuestion $quizQuestion)
	{
		$this->dispatch(new DetachReactions($quizQuestion));
		$this->dispatch(new DeleteModels($quizQuestion->answers));
	}
}
