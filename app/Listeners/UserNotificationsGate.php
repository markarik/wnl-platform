<?php namespace App\Listeners;

use App\Events\Event;
use App\Models\Role;
use App\Models\User;
use App\Models\UserCourseProgress;
use App\Notifications\EventNotification;
use App\Notifications\EventTaskNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class UserNotificationsGate implements ShouldQueue
{
	use InteractsWithQueue;

	public $queue = 'notifications';

	const CHANNELS = [
		'role'           => 'private-group.%s',
		'private'        => 'private-%d',
		'private-stream' => 'private-stream.%d',
	];

	public function notifyPrivate($user, $event)
	{
		$channelFormatted = sprintf(self::CHANNELS['private'], $user->id);
		$user->notify(new EventNotification($event, $channelFormatted));
	}

	public function notifyPrivateStream(array $excluded, $event)
	{
		$excluded = array_filter($excluded, function($item) {
			return !is_null($item);
		});

		$progress = $this->usersLessonProgress($event);

		$users = User::select()
			->whereNotIn('id', $excluded)
			->get();

		$users = $users->filter(function($user) {
			/** @var User $user */
			return $user->subscription && ($user->subscription->subscription_status === 'active');
		});

		if ($progress) {
			$users = $users->filter(function ($user) use ($progress) {
				return in_array($user->id, $progress);
			});
		}


		foreach ($users as $user) {
			if ($this->shouldStopNotification($event)) {
				$this->delete();
				break;
			}

			$channelFormatted = sprintf(self::CHANNELS['private-stream'], $user->id);
			$user->notify(new EventNotification($event, $channelFormatted));
		}
	}

	protected function usersLessonProgress($event)
	{
		$lessonId = $event->data['context']['params']['lessonId'] ?? null;
		if (!$lessonId) {
			return false;
		}

		$usersIds = UserCourseProgress::select(['user_id'])
			->where('lesson_id', $lessonId)
			->get()
			->pluck('user_id')
			->toArray();

		return $usersIds;
	}

	/**
	 * Notify all moderators about an event.
	 *
	 * @param Event $event
	 *
	 * @return bool
	 */
	public function notifyModerators($event)
	{
		$actor = User::find($event->data['actors']['id']);
		if ($actor->isModerator() || $actor->isAdmin()) {
			return false;
		}

		$team = 'moderators';
		$group = Role::byName('moderator');
		$channelFormatted = sprintf(self::CHANNELS['role'], $team);
		$notification = new EventTaskNotification($event, $channelFormatted, $team);
		Notification::send($group, $notification);

		return true;
	}

	/**
	 * Handle the event.
	 *
	 * @param Event $event
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function handle($event)
	{
		if (method_exists($event, 'transform')) {
			$event->transform();
		}

		$handler = app('App\Listeners\Handlers\\' . class_basename($event) . 'Handler');
		$handler->handle($event, $this);
	}

	private function shouldStopNotification($event) {
		if (!$event->data['subject']['id']) {
			return false;
		}

		return empty($event->model::query()->find($event->data['subject']['id']));
	}
}
