<?php

namespace App\Policies\Course;

use App\Models\User;
use App\Models\Screen;
use Illuminate\Auth\Access\HandlesAuthorization;

class ScreensPolicy
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
	 * Determine whether the user can view the screen.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Screen $screen
	 * @return mixed
	 */
	public function view(User $user, Screen $screen)
	{
		return true;
	}

	/**
	 * Determine whether the user can create screens.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user)
	{
		return false;
	}

	/**
	 * Determine whether the user can update the screen.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Screen $screen
	 * @return mixed
	 */
	public function update(User $user, Screen $screen)
	{
		return false;
	}

	/**
	 * Determine whether the user can delete the screen.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Screen $screen
	 * @return mixed
	 */
	public function delete(User $user, Screen $screen)
	{
		return false;
	}
}
