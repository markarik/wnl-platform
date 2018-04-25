<?php

namespace App\Policies\User;

use App\Models\User;
use App\Models\UserCourseProgress;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserCourseProgressPolicy
{
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view the userCourseProgress.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\UserCourseProgress $userSettings
	 * @return boolean
	 */
	public function view(User $user, UserCourseProgress $userCourseProgress)
	{
		return $user->id === $userCourseProgress->user_id;
	}

	/**
	 * Determine whether the user can delete the userCourseProgress.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\UserCourseProgress $userCourseProgress
	 * @return boolean
	 */
	public function delete(User $user, UserCourseProgress $userCourseProgress)
	{
		return $user->profile->id === $userCourseProgress->user_id;
	}
}
