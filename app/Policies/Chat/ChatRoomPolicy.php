<?php

namespace App\Policies\Chat;

use App\Models\User;
use App\Models\ChatRoom;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatRoomPolicy
{
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view the chatRoom.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\ChatRoom $chatRoom
	 * @return mixed
	 */
	public function view(User $user, ChatRoom $chatRoom)
	{
		return $chatRoom->is_public;
	}

	/**
	 * Determine whether the user can create chatRooms.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user)
	{
		return false;
	}

	/**
	 * Determine whether the user can update the chatRoom.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\ChatRoom $chatRoom
	 * @return mixed
	 */
	public function update(User $user, ChatRoom $chatRoom)
	{
		return false;
	}

	/**
	 * Determine whether the user can delete the chatRoom.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\ChatRoom $chatRoom
	 * @return mixed
	 */
	public function delete(User $user, ChatRoom $chatRoom)
	{
		return false;
	}
}