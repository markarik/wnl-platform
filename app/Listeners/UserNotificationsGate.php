<?php namespace App\Listeners;

use App\Models\User;
use App\Notifications\EventNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class UserNotificationsGate implements ShouldQueue
{
	const CHANNELS = [
		'moderators' => 'moderators',
		'private'    => 'private-%d',
		'stream'     => 'private-stream-%d',
	];

	public function notifyPrivate($user, $event)
	{
		$channelFormatted = sprintf(self::CHANNELS['private'], $user->id);
		$user->notify(new EventNotification($event, $channelFormatted));
	}

	public function notifyPrivateStream($user, $event)
	{
		$channelFormatted = sprintf(self::CHANNELS['stream'], $user->id);
		$user->notify(new EventNotification($event, $channelFormatted));
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

		$moderators = User::ofRole('moderator');
		$notification = new EventNotification($event, self::CHANNELS['moderators']);
		Notification::send($moderators, $notification);

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
}
