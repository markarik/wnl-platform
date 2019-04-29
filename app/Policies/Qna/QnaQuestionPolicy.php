<?php

namespace App\Policies\Qna;

use App\Models\User;
use App\Models\QnaQuestion;
use Illuminate\Auth\Access\HandlesAuthorization;

class QnaQuestionPolicy
{
	use HandlesAuthorization;

	public function before($user, $ability)
	{
		if ($user->isAdmin() || $user->isModerator()) {
			return true;
		}
	}

	/**
	 * Determine whether the user can view the qnaQuestion.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\QnaQuestion $qnaQuestion
	 * @return mixed
	 */
	public function view(User $user, QnaQuestion $qnaQuestion)
	{
		return true;
	}

	/**
	 * Determine whether the user can create qnaQuestions.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user)
	{
		return true;
	}

	/**
	 * Determine whether the user can update the qnaQuestion.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\QnaQuestion $qnaQuestion
	 * @return mixed
	 */
	public function update(User $user, QnaQuestion $qnaQuestion)
	{
		return $user->id === $qnaQuestion->user_id;
	}

	/**
	 * Determine whether the user can delete the qnaQuestion.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\QnaQuestion $qnaQuestion
	 * @return mixed
	 */
	public function delete(User $user, QnaQuestion $qnaQuestion)
	{
		return $user->id === $qnaQuestion->user_id;
	}
}
