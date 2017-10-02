<?php namespace App\Listeners\Handlers;


use App\Events\CommentRestored;
use App\Listeners\UserNotificationsGate;
use App\Models\User;
use App\Models\Notification;

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
			$notification->delete();
			$gate->notifyPrivate($commentAuthor, $event);
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
