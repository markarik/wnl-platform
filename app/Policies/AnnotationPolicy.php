<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnnotationPolicy
{
	use HandlesAuthorization;

	public function before($user)
	{
		return $user->isAdmin();
	}

	public function delete(User $user)
	{
		return $user->isAdmin();
	}
}
