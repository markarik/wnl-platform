<?php namespace App\Listeners;

use Notification;
use App\Models\User;
use App\Notifications\EventNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyUser implements ShouldQueue
{
	/**
	 * Handle the event.
	 *
	 * @param $event
	 * @return void
	 */
	public function handle($event)
	{
		$this->{'handle' . class_basename($event)}($event);
	}

	/**
	 * Handle notifications for AnswerPosted event.
	 *
	 * @param $event
	 */
	private function handleAnswerPosted($event)
	{
		$this->notifyModerators($event);

		$user = $event->qnaAnswer->question->user;

		$event->data = [
			'event'   => 'qna-answer-posted',
			'objects' => [
				'type' => 'qna_question',
				'id'   => $event->qnaAnswer->question->id,
				'text' => $event->qnaAnswer->question->text,
			],
			'actors'  => [
				'id'         => $event->qnaAnswer->user->id,
				'first_name' => $event->qnaAnswer->user->first_name,
				'last_name'  => $event->qnaAnswer->user->last_name,
			],
		];

		$user->notify(new EventNotification($event));
	}

	/**
	 * Handle notifications for QuestionPosted event.
	 *
	 * @param $event
	 */
	public function handleQuestionPosted($event)
	{
		$this->notifyModerators($event);
	}

	/**
	 * Handle notifications for CommentPosted event.
	 *
	 * @param $event
	 */
	public function handleCommentPosted($event)
	{
		$comment = $event->comment;

		$event->data = [
			'event'   => 'comment-posted',
			'objects' => [
				'type' => snake_case(class_basename($comment->commentable)),
				'id'   => $comment->commentable->id,
			],
		];

		if ($actor = $comment->commentable->user) {
			$event->data['actors'] = [
				'id'         => $actor->id,
				'first_name' => $actor->first_name,
				'last_name'  => $actor->last_name,
			];
		}

		$this->notifyModerators($event);
	}

	/**
	 * Handle notifications for ReactionAdded event.
	 * @param $event
	 */
	public function handleReactionAdded($event)
	{

	}

	/**
	 * Handle notifications for PrivateMessageSent event.
	 *
	 * @param $event
	 */
	public function handlePrivateMessageSent($event)
	{

	}

	/**
	 * Notify all moderators about an event.
	 *
	 * @param $event
	 */
	private function notifyModerators($event)
	{
		$moderators = User::ofRole('moderator');

		foreach ($moderators as $moderator) {
			$moderator->notify(new EventNotification($event));
		}
	}
}
