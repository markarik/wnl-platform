<?php

namespace App\Policies\Qna;

use App\Models\User;
use App\Models\QnaAnswer;
use Illuminate\Auth\Access\HandlesAuthorization;

class QnaAnswerPolicy
{
	use HandlesAuthorization;

	public function before($user, $ability)
	{
		if ($user->isAdmin() || $user->isModerator()) {
			return true;
		}
	}

	/**
	 * Determine whether the user can view the qnaAnswer.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\QnaAnswer $qnaAnswer
	 * @return mixed
	 */
	public function view(User $user, QnaAnswer $qnaAnswer)
	{
		return true;
	}

	/**
	 * Determine whether the user can create qnaAnswers.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user)
	{
		return true;
	}

	/**
	 * Determine whether the user can update the qnaAnswer.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\QnaAnswer $qnaAnswer
	 * @return mixed
	 */
	public function update(User $user, QnaAnswer $qnaAnswer)
	{
		return $user->id === $qnaAnswer->user_id;
	}

	/**
	 * Determine whether the user can delete the qnaAnswer.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\QnaAnswer $qnaAnswer
	 * @return mixed
	 */
	public function delete(User $user, QnaAnswer $qnaAnswer)
	{
		return $user->id === $qnaAnswer->user_id;
	}
}
