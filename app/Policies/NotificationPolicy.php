<?php

namespace App\Policies;

use Auth;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotificationPolicy
{
	use HandlesAuthorization;

	public function before($user, $ability)
	{
		if ($user->isAdmin() || $user->isModerator()) {
			return true;
		}

		return null;
	}

	public function viewMultiple(User $user)
	{
		return $user->id === Auth::user()->id;
	}

	/**
	 * Determine whether the user can view the notification.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Notification $notification
	 *
	 * @return mixed
	 */
	public function view(User $user, Notification $notification)
	{
		return
			$user->id === $notification->notifiable_type &&
			$notification->notifiable_type === 'App\\Models\\User';
	}

	/**
	 * Determine whether the user can create notifications.
	 *
	 * @param  \App\Models\User $user
	 *
	 * @return mixed
	 */
	public function create(User $user)
	{
		return false;
	}

	/**
	 * Determine whether the user can update the notification.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Notification $notification
	 *
	 * @return mixed
	 */
	public function update(User $user, Notification $notification)
	{
		return
			$user->id === $notification->notifiable_id &&
			$notification->notifiable_type === 'App\\Models\\User';
	}

	public function updateMultiple(User $user, $notifications)
	{
		foreach ($notifications as $notification) {
			if (!$this->update($user, $notification)){
				return false;
			}
		}

		return true;
	}

	/**
	 * Determine whether the user can delete the notification.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Notification $notification
	 *
	 * @return mixed
	 */
	public function delete(User $user, Notification $notification)
	{
		return false;
	}
}
