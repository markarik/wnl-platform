<?php

namespace App\Policies\User;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserAddressPolicy
{
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view the userAddress.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\UserAddress $userAddress
	 * @return mixed
	 */
	public function view(User $user, UserAddress $userAddress)
	{
		return $user->id === $userAddress->user_id;
	}

	/**
	 * Determine whether the user can create userAddresses.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user)
	{
		return false;
	}

	/**
	 * Determine whether the user can update the userAddress.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\UserAddress $userAddress
	 * @return mixed
	 */
	public function update(User $user, UserAddress $userAddress)
	{
		return $user->id === $userAddress->user_id || $user->isAdmin();
	}

	/**
	 * Determine whether the user can delete the userAddress.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\UserAddress $userAddress
	 * @return mixed
	 */
	public function delete(User $user, UserAddress $userAddress)
	{
		return false;
	}
}
