<?php

namespace App\Policies\User;

use App\Models\User;
use App\Models\UserProductState;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserProductStatePolicy
{
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view the userProductState.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\UserProductState $userProductState
	 * @return mixed
	 */
	public function view(User $user, UserProductState $userProductState)
	{
		return $user->id === $userProductState->user_id;
	}

	/**
	 * Determine whether the user can create userProductState.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user)
	{
		return true;
	}

	/**
	 * Determine whether the user can update the userProductState.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\UserProductState $userProductState
	 * @return mixed
	 */
	public function update(User $user, UserProductState $userProductState)
	{
		return $user->id === $userProductState->user_id;
	}

	/**
	 * Determine whether the user can delete the userProductState.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\UserProductState $userProductState
	 * @return mixed
	 */
	public function delete(User $user, UserProductState $userProductState)
	{
		return false;
	}
}
