<?php

namespace App\Policies\User;

use App\User;
use App\UserBilling;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserUserBillingPolicy
{
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view the userBilling.
	 *
	 * @param  \App\User $user
	 * @param  \App\UserBilling $userBilling
	 * @return mixed
	 */
	public function view(User $user, UserBilling $userBilling)
	{
		//
	}

	/**
	 * Determine whether the user can create userBillings.
	 *
	 * @param  \App\User $user
	 * @return mixed
	 */
	public function create(User $user)
	{
		//
	}

	/**
	 * Determine whether the user can update the userBilling.
	 *
	 * @param  \App\User $user
	 * @param  \App\UserBilling $userBilling
	 * @return mixed
	 */
	public function update(User $user, UserBilling $userBilling)
	{
		//
	}

	/**
	 * Determine whether the user can delete the userBilling.
	 *
	 * @param  \App\User $user
	 * @param  \App\UserBilling $userBilling
	 * @return mixed
	 */
	public function delete(User $user, UserBilling $userBilling)
	{
		//
	}
}
