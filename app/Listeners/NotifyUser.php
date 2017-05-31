<?php namespace App\Listeners;

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
				'text' => str_limit($event->qnaAnswer->question->text, self::TEXT_LIMIT),
			],
			'subject' => [
				'type' => 'qna_answer',
				'id'   => $event->qnaAnswer->id,
				'text' => str_limit($event->qnaAnswer->text, self::TEXT_LIMIT),
			],
			'actors'  => [
				'id'         => $event->qnaAnswer->user->id,
				'first_name' => $event->qnaAnswer->user->first_name,
				'last_name'  => $event->qnaAnswer->user->last_name,
				'avatar'     => $event->qnaAnswer->user->profile->avatar,
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
		$event->data = [
			'event'   => 'qna-question-posted',
			'subject' => [
				'type' => 'qna_question',
				'id'   => $event->qnaQuestion->id,
				'text' => str_limit($event->qnaQuestion->text, self::TEXT_LIMIT),
			],
			'actors'  => [
				'id'         => $event->qnaQuestion->user->id,
				'first_name' => $event->qnaQuestion->user->first_name,
				'last_name'  => $event->qnaQuestion->user->last_name,
				'avatar'     => $event->qnaQuestion->user->profile->avatar,
			],
		];

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

		$comment->commentable->user->notify(new EventNotification($event));
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

		Notification::send($moderators, new EventNotification($event));
	}
}
