<?php

namespace App\Policies\User;

use App\Models\User;
use App\Models\UserBillingData;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserBillingPolicy
{
	use HandlesAuthorization;

	public function before($user, $ability) {
		if ($user->isAdmin()) {
			return true;
		}

		return null;
	}

	/**
	 * Determine whether the user can view the userBilling.
	 *
	 * @param  User $user
	 * @param  UserBillingData $userBilling
	 * @return mixed
	 */
	public function view(User $user, UserBillingData $userBilling)
	{
		return $user->id === $userBilling->user_id;
	}

	/**
	 * Determine whether the user can create userBillings.
	 *
	 * @param  User $user
	 * @return mixed
	 */
	public function create(User $user)
	{
		return false;
	}

	/**
	 * Determine whether the user can update the userBilling.
	 *
	 * @param  User $user
	 * @param  UserBillingData $userBilling
	 * @return mixed
	 */
	public function update(User $user, UserBillingData $userBilling)
	{
		return $user->id === $userBilling->user_id;
	}

	/**
	 * Determine whether the user can delete the userBilling.
	 *
	 * @param  User $user
	 * @param  UserBillingData $userBilling
	 * @return mixed
	 */
	public function delete(User $user, UserBillingData $userBilling)
	{
		return $user->id === $userBilling->id;
	}
}
