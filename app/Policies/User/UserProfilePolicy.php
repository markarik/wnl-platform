<?php

namespace App\Policies\User;

use App\User;
use App\UserProfile;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserProfilePolicy
{
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view the userProfile.
	 *
	 * @param  \App\User $user
	 * @param  \App\UserProfile $userProfile
	 * @return mixed
	 */
	public function view(User $user, UserProfile $userProfile)
	{
		//
	}

	/**
	 * Determine whether the user can create userProfiles.
	 *
	 * @param  \App\User $user
	 * @return mixed
	 */
	public function create(User $user)
	{
		//
	}

	/**
	 * Determine whether the user can update the userProfile.
	 *
	 * @param  \App\User $user
	 * @param  \App\UserProfile $userProfile
	 * @return mixed
	 */
	public function update(User $user, UserProfile $userProfile)
	{
		//
	}

	/**
	 * Determine whether the user can delete the userProfile.
	 *
	 * @param  \App\User $user
	 * @param  \App\UserProfile $userProfile
	 * @return mixed
	 */
	public function delete(User $user, UserProfile $userProfile)
	{
		//
	}
}
