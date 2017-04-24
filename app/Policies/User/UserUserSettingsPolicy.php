<?php

namespace App\Policies\User;

use App\User;
use App\UserSettings;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserUserSettingsPolicy
{
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view the userSettings.
	 *
	 * @param  \App\User $user
	 * @param  \App\UserSettings $userSettings
	 * @return mixed
	 */
	public function view(User $user, UserSettings $userSettings)
	{
		//
	}

	/**
	 * Determine whether the user can create userSettings.
	 *
	 * @param  \App\User $user
	 * @return mixed
	 */
	public function create(User $user)
	{
		//
	}

	/**
	 * Determine whether the user can update the userSettings.
	 *
	 * @param  \App\User $user
	 * @param  \App\UserSettings $userSettings
	 * @return mixed
	 */
	public function update(User $user, UserSettings $userSettings)
	{
		//
	}

	/**
	 * Determine whether the user can delete the userSettings.
	 *
	 * @param  \App\User $user
	 * @param  \App\UserSettings $userSettings
	 * @return mixed
	 */
	public function delete(User $user, UserSettings $userSettings)
	{
		//
	}
}
