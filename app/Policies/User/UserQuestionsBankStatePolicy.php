<?php

namespace App\Policies\User;

use App\Models\User;
use App\Models\UserQuestionsBankState;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserQuestionsBankStatePolicy
{
	use HandlesAuthorization;

	/**
	 * @param \App\Models\User $user
	 * @param \App\Models\UserQuestionsBankState $state
	 * @return boolean
	 */
	public function view(User $user, UserQuestionsBankState $state)
	{
		return $user->id === $state->user_id;
	}

	/**
	 * @param \App\Models\User $user
	 * @param \App\Models\UserQuestionsBankState $state
	 * @return boolean
	 */
	public function update(User $user, UserQuestionsBankState $state)
	{
		return $user->id === $state->user_id;
	}
}
