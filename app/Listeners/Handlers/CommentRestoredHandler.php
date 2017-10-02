<?php namespace App\Listeners\Handlers;


use App\Events\CommentRestored;
use App\Listeners\UserNotificationsGate;
use App\Models\Notification;
use Illuminate\Support\Facades\Notification as Notify;
use App\Notifications\EventNotification;
use App\Notifications\Media\LiveChannel;

class CommentRestoredHandler
{
	/**
	 * Notification rules for CommentRestored event.
	 *
	 * @param CommentRestored $event
	 * @param UserNotificationsGate $gate
	 */
	public function handle(CommentRestored $event, UserNotificationsGate $gate)
	{
		$commentAuthor = $event->comment->user;
		$commentRemover = $event->data['actors']['id'] ?? 0;

		if ($commentAuthor->id !== $commentRemover) {
			$notification = $this->getNotification($event->comment);

			$event->id = $notification->event_id;

			$newNotification = new EventNotification($event, $notification->channel);
			$newNotification->id = $notification->id;
			$newNotification->event = $event;

			Notify::sendNow($commentAuthor, $newNotification, [LiveChannel::class]);

			$notification->delete();
		}
	}

	private function getNotification($model) {
		return Notification::select()
		->where(function ($query) use ($model) {
			$query
				->whereRaw('data->"$.event" = "comment-resolved"')
				->whereRaw('data->"$.objects.id" = ' . $model->id)
				->whereRaw('data->"$.objects.type" = "comment"');
		})
		->first();
	}
}
