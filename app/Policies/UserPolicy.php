<?php

namespace App\Policies;

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
		return true;
	}

	/**
	 * Determine whether the user can create users.
	 *
	 * @param  \App\Models\User $user
	 * @return bool
	 */
	public function create(User $user)
	{
		return true;
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
		return true;
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
		return true;
	}
}
