<?php

namespace App\Policies\Course;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CourseStructureNodePolicy
{
	use HandlesAuthorization;

	/**
	 * @param User $user
	 * @return bool|null
	 */
	public function before($user)
	{
		if ($user->isAdmin()) {
			return true;
		}

		return null;
	}

	public function delete()
	{
		return false;
	}
}
