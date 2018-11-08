<?php

namespace App\Policies\Task;

use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
	use HandlesAuthorization;

	public function before($user, $ability)
	{
		if ($user->isAdmin() || $user->isModerator()) {
			return true;
		}

		return null;
	}

	public function get()
	{
		return false;

	}
	public function update()
	{
		return false;
	}
}
