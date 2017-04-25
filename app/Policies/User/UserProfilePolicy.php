<?php

namespace App\Policies\User;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserProfilePolicy
{
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view the userProfile.
	 *
	 * @param  User $user
	 * @param  UserProfile $userProfile
	 * @return mixed
	 */
	public function view(User $user, UserProfile $userProfile)
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
	 * @param  UserProfile $userProfile
	 * @return mixed
	 */
	public function update(User $user, UserProfile $userProfile)
	{
		return $user->id === $userProfile->user_id;
	}

	/**
	 * Determine whether the user can delete the userProfile.
	 *
	 * @param  User $user
	 * @param  UserProfile $userProfile
	 * @return mixed
	 */
	public function delete(User $user, UserProfile $userProfile)
	{
		// TODO: Profile should be deleted together with main User entity.
		return false;
	}
}
