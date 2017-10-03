<?php namespace App\Listeners\Handlers;


use App\Events\QnaQuestionRestored;
use App\Listeners\UserNotificationsGate;
use App\Models\Notification;
use Illuminate\Support\Facades\Notification as Notify;
use App\Notifications\EventNotification;
use App\Notifications\Media\LiveChannel;

class QnaQuestionRestoredHandler
{
	/**
	 * Notification rules for QnaQuestionRestored event.
	 *
	 * @param QnaQuestionRestored $event
	 * @param UserNotificationsGate $gate
	 */
	public function handle(QnaQuestionRestored $event, UserNotificationsGate $gate)
	{
		$qnaAuthor = $event->qnaQuestion->user;
		$qnaRemover = $event->data['actors']['id'] ?? 0;

		if ($qnaAuthor->id !== $qnaRemover) {
			$notification = $this->getNotification($event->qnaQuestion);

			$event->id = $notification->event_id;

			$newNotification = new EventNotification($event, $notification->channel);
			$newNotification->id = $notification->id;
			$newNotification->event = $event;

			Notify::sendNow($qnaAuthor, $newNotification, [LiveChannel::class]);

			$notification->delete();
		}
	}

	private function getNotification($model) {
		return Notification::select()
		->where(function ($query) use ($model) {
			$query
				->whereRaw('data->"$.event" = "qna-question-resolved"')
				->whereRaw('data->"$.objects.id" = ' . $model->id)
				->whereRaw('data->"$.objects.type" = "qna_question"');
		})
		->first();
	}
}
