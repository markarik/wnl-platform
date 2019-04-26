<?php

namespace App\Policies\User;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfilePolicy
{
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view the userProfile.
	 *
	 * @param  User $user
	 * @param  Profile $userProfile
	 * @return mixed
	 */
	public function view(User $user, Profile $userProfile)
	{
		// Anyone can see your public profile.
		return true;
	}

	/**
	 * Determine whether the user can create userProfiles.
	 *
	 * @param  User $user
	 * @return mixed
	 */
	public function create(User $user)
	{
		// Profile is created automatically when someone signs up.
		return false;
	}

	/**
	 * Determine whether the user can update the userProfile.
	 *
	 * @param  User $user
	 * @param  Profile $userProfile
	 * @return mixed
	 */
	public function update(User $user, Profile $userProfile)
	{
		return $user->id === $userProfile->user_id;
	}

	/**
	 * Determine whether the user can delete the userProfile.
	 *
	 * @param  User $user
	 * @param  Profile $userProfile
	 * @return mixed
	 */
	public function delete(User $user, Profile $userProfile)
	{
		// TODO: Profile should be deleted together with main User entity.
		return false;
	}
}
