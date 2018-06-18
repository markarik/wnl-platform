<?php namespace App\Listeners;

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
		$progress = $this->usersLessonProgress($event);
		$users = User::all()->filter(function ($user) use ($excluded) {
			return !in_array($user->id, $excluded);
		});
		if ($progress) {
			$users = $users->filter(function ($user) use ($progress) {
				return in_array($user->id, $progress);
			});
		}

		foreach ($users as $user) {
			$channelFormatted = sprintf(self::CHANNELS['private-stream'], $user->id);
			$this->stopNotification($event);
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
	 * @param $event
	 *
	 * @return bool
	 */
	public function notifyModerators($event)
	{
		$actor = User::find($event->data['actors']['id']);
		if ($actor->hasRole('moderator') || $actor->hasRole('admin')) {
			return false;
		}

		$team = 'moderators';
		$group = Role::byName('moderator');
		$channelFormatted = sprintf(self::CHANNELS['role'], $team);
		$notification = new EventTaskNotification($event, $channelFormatted, $team);
		Notification::send($group, $notification);

		// For some reason event is not deserialized here by default
		// calling __wakeup() forces an event to deserialize, hence we can access question and user property
		// ...looks like it's being serialized after calling 'notifyModerators', so I moved the wakeup here.
		$event->__wakeup();

		return true;
	}

	/**
	 * Handle the event.
	 *
	 * @param $event
	 *
	 * @return void
	 */
	public function handle($event)
	{
		if (method_exists($event, 'transform')) {
			$event->transform();
		}

		$handler = app('App\Listeners\Handlers\\' . class_basename($event) . 'Handler');
		$handler->handle($event, $this);
	}

	private function stopNotification($event) {
		$modelExists = !empty($event->model::query()->find($event->data['subject']['id']));

		\Log::debug('***************');
		if (!$modelExists) {
			\Log::debug('KILLING NOTIFICATION');
			$this->delete();
		}
		\Log::debug('***************');
	}
}
