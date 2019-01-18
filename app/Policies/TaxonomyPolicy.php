<?php

namespace App\Policies;

use App\Models\Taxonomy;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxonomyPolicy
{
    use HandlesAuthorization;

	public function before($user, $ability)
	{
		if ($user->isAdmin()) {
			return true;
		}

		return null;
	}

	/**
	 * Determine whether the user can delete the Taxonomy.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Taxonomy $taxonomy
	 * @return mixed
	 */
	public function delete(User $user, Taxonomy $taxonomy)
	{
		return false;
	}
}
