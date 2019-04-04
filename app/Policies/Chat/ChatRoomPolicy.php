<?php

namespace App\Policies\Chat;

use App\Contracts\CourseProvider;
use App\Models\ChatRoom;
use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatRoomPolicy
{
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view the chatRoom.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\ChatRoom $chatRoom
	 *
	 * @return mixed
	 */
	public function view(User $user, ChatRoom $chatRoom)
	{
		if ($chatRoom->requires('lesson_access')) {
			return $this->checkLessonAccess($user, $chatRoom);
		}

		if ($chatRoom->requires('role_access')) {
			return $this->checkRoleAccess($user, $chatRoom);
		}

		return
			$chatRoom->is_public ||
			$chatRoom->users->contains($user);
	}

	/**
	 * Determine whether the user can create chatRooms.
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
	 * Determine whether the user can update the chatRoom.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\ChatRoom $chatRoom
	 *
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
	 *
	 * @return mixed
	 */
	public function delete(User $user, ChatRoom $chatRoom)
	{
		return false;
	}

	/**
	 * @param User $user
	 * @param ChatRoom $chatRoom
	 * @return bool
	 */
	protected function checkLessonAccess($user, $chatRoom)
	{
		if (Course::find((new CourseProvider)->getCourseId())->is_plan_builder_enabled) {
			// Allow users to access chat to all lessons when plan builder is enabled
			return true;
		}

		foreach ($chatRoom->lessons as $lesson) {
			if ($lesson->isAvailable($user)) {
				return true;
			}
		}

		return false;
	}

	protected function checkRoleAccess($user, $chatRoom)
	{
		$userRoles = $user->roles;
		foreach ($chatRoom->roles as $role) {
			if ($userRoles->contains($role)) {
				return true;
			}
		}

		return false;
	}
}
