<?php

namespace App\Policies\Task;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
	use HandlesAuthorization;

	public function before($user, $ability)
	{
		if ($user->hasRole('admin') || $user->hasRole('moderator')) {
			return true;
		}

		return null;
	}
}
