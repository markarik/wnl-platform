<?php namespace App\Listeners;

use App\Events\CommentPosted;
use App\Events\Qna\AnswerPosted;
use App\Events\Qna\QuestionPosted;
use App\Events\ReactionAdded;
use App\Models\User;
use App\Notifications\EventNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotifyUser implements ShouldQueue
{
	const TEXT_LIMIT = 160;

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
		$this->{'handle' . class_basename($event)}($event);
	}

	/**
	 * Handle notifications for AnswerPosted event.
	 *
	 * @param $event
	 */
	private function handleAnswerPosted(AnswerPosted $event)
	{
		$this->notifyModerators($event);

		$user = $event->qnaAnswer->question->user;
		$user->notify(new EventNotification($event));
	}

	/**
	 * Handle notifications for QuestionPosted event.
	 *
	 * @param $event
	 */
	public function handleQuestionPosted(QuestionPosted $event)
	{
		$this->notifyModerators($event);
	}

	/**
	 * Handle notifications for CommentPosted event.
	 *
	 * @param $event
	 */
	public function handleCommentPosted(CommentPosted $event)
	{
		$this->notifyModerators($event);

		$commentableAuthor = $event->comment->commentable->user;

		if ($commentableAuthor) {
			$commentableAuthor->notify(new EventNotification($event));
		}
	}

	/**
	 * Handle notifications for ReactionAdded event.
	 *
	 * @param $event
	 */
	public function handleReactionAdded(ReactionAdded $event)
	{
		$reactableAuthor = $event->reactable->user;
		if ($reactableAuthor && $event->reaction->type !== 'bookmark') {
			$reactableAuthor->notify(new EventNotification($event));
		}
	}

	/**
	 * Handle notifications for PrivateMessageSent event.
	 *
	 * @param $event
	 */
	public function handlePrivateMessageSent($event)
	{
		// :(
	}

	/**
	 * Notify all moderators about an event.
	 *
	 * @param $event
	 *
	 * @return bool
	 */
	private function notifyModerators($event)
	{
		$actor = User::find($event->data['actors']['id']);
		if ($actor->hasRole('moderator') || $actor->hasRole('admin')) {
			return false;
		}

		$moderators = User::ofRole('moderator');

		Notification::send($moderators, new EventNotification($event));

		// For some reason event is not deserialized here by default
		// calling __wakeup() forces an event to deserialize, hence we can access question and user property
		// ...looks like it's being serialized after calling 'notifyModerators', so I moved the wakeup here.
		$event->__wakeup();

		return true;
	}
}
