<?php

namespace App\Policies\Course;

use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuizQuestionPolicy
{
	use HandlesAuthorization;

	public function before($user, $ability)
	{
		if ($user->isAdmin()) {
			return true;
		}

		return false;
	}

	/**
	 * Determine whether the user can view the QuizQuestion.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\QuizQuestion $quizQuestion
	 * @return mixed
	 */
	public function view(User $user, QuizQuestion $quizQuestion)
	{
		return true;
	}

	/**
	 * Determine whether the user can create QuizQuestions.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user)
	{
		return false;
	}

	/**
	 * Determine whether the user can update the QuizQuestion.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\QuizQuestion $quizQuestion
	 * @return mixed
	 */
	public function update(User $user, QuizQuestion $quizQuestion)
	{
		return false;
	}

	/**
	 * Determine whether the user can delete the QuizQuestion.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\QuizQuestion $quizQuestion
	 * @return mixed
	 */
	public function delete(User $user, QuizQuestion $quizQuestion)
	{
		return false;
	}
}
