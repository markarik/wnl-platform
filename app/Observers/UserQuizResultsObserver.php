<?php

namespace App\Observers;

use App\Models\UserQuizResults;
use App\Models\QuizQuestion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UserQuizResultsObserver
{

	public function saved(UserQuizResults $results)
	{
		$quizQuestionId = $results->quiz_question_id;
		if (!isset($quizQuestionId) || !isset($results->user_id)) {
			return false;
		}

		$reactable = QuizQuestion::find($quizQuestionId);
		$reaction = Reaction::type('bookmark');
		$now = Carbon::now();

		$reactable->reactions()->attach($reaction, [
			'user_id'    => $results->user_id,
			'created_at' => $now,
			'updated_at' => $now,
		]);
	}
}
