<?php

namespace App\Observers;

use App\Jobs\DeleteModels;
use App\Jobs\DetachReactions;
use App\Models\QuizQuestion;
use Illuminate\Foundation\Bus\DispatchesJobs;

class QuizQuestionObserver
{
	use DispatchesJobs;


	public function created(QuizQuestion $quizQuestion)
	{
		$quizQuestion->searchable();
	}

	public function updated(QuizQuestion $quizQuestion)
	{
		$quizQuestion->searchable();
	}

	public function deleted(QuizQuestion $quizQuestion)
	{
		if ($quizQuestion->isForceDeleting()) {
			$quizQuestion->unsearchable();
			$this->dispatch(new DetachReactions($quizQuestion));
			$this->dispatch(new DeleteModels($quizQuestion->answers));
		}
	}
}
