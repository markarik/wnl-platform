<?php

namespace App\Policies\User;

use App\Models\User;
use App\Models\UserSettings;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserSettingsPolicy
{
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view the userSettings.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\UserSettings $userSettings
	 * @return mixed
	 */
	public function view(User $user, UserSettings $userSettings)
	{
		return $user->id === $userSettings->user_id;
	}

	/**
	 * Determine whether the user can create userSettings.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user)
	{
		return false;
	}

	/**
	 * Determine whether the user can update the userSettings.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\UserSettings $userSettings
	 * @return mixed
	 */
	public function update(User $user, UserSettings $userSettings)
	{
		return $user->id === $userSettings->user_id;
	}

	/**
	 * Determine whether the user can delete the userSettings.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\UserSettings $userSettings
	 * @return mixed
	 */
	public function delete(User $user, UserSettings $userSettings)
	{
		return false;
	}
}
