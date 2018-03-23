<?php

namespace App\Policies\Course;

use App\Models\User;
use App\Models\UserLessonAvailability;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserLessonAvailabilityPolicy
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
	 * Determine whether the user can update the lessonAvailabilitiy.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\UserLessonAvailability $screen
	 * @return mixed
	 */
	public function update(User $user, UserLessonAvailability $userLessonAvailability)
	{
		return $user->id === $userLessonAvailability->user_id;
	}

}
