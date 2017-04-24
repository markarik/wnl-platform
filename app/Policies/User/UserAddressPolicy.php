<?php

namespace App\Policies\User;

use App\User;
use App\UserAddress;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserAddressPolicy
{
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view the userAddress.
	 *
	 * @param  \App\User $user
	 * @param  \App\UserAddress $userAddress
	 * @return mixed
	 */
	public function view(User $user, UserAddress $userAddress)
	{
		//
	}

	/**
	 * Determine whether the user can create userAddresses.
	 *
	 * @param  \App\User $user
	 * @return mixed
	 */
	public function create(User $user)
	{
		//
	}

	/**
	 * Determine whether the user can update the userAddress.
	 *
	 * @param  \App\User $user
	 * @param  \App\UserAddress $userAddress
	 * @return mixed
	 */
	public function update(User $user, UserAddress $userAddress)
	{
		//
	}

	/**
	 * Determine whether the user can delete the userAddress.
	 *
	 * @param  \App\User $user
	 * @param  \App\UserAddress $userAddress
	 * @return mixed
	 */
	public function delete(User $user, UserAddress $userAddress)
	{
		//
	}
}
