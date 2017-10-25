<?php

namespace App\Policies\User;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view the user.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\User $targetUser
	 * @return bool
	 */
	public function view(User $user, User $targetUser)
	{
		return $user->id === $targetUser->id || $user->isAdmin();
	}

	/**
	 * Determine whether the user can create users.
	 *
	 * @param  \App\Models\User $user
	 * @return bool
	 */
	public function create(User $user)
	{
		return false;
	}

	/**
	 * Determine whether the user can update the user.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\User $targetUser
	 * @return bool
	 */
	public function update(User $user, User $targetUser)
	{
		return $user->id === $targetUser->id;
	}

	/**
	 * Determine whether the user can delete the user.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\User $targetUser
	 * @return bool
	 */
	public function delete(User $user, User $targetUser)
	{
		return $user->id === $targetUser->id;
	}
}
