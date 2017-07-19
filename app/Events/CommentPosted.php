<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CommentPosted extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		SerializesModels,
		SanitizesUserContent;

	const TEXT_LIMIT = 160;

	public $comment;

	/**
	 * Create a new event instance.
	 *
	 * @param Comment $comment
	 */
	public function __construct(Comment $comment)
	{
		parent::__construct();
		$this->comment = $comment;
	}

	public function transform()
	{
		$comment = $this->comment;
		$actor = $comment->user;
		$commentableType = snake_case(class_basename($comment->commentable));

		$this->data = [
			'event'   => 'comment-posted',
			'objects' => [
				'type' => $commentableType,
				'id'   => $comment->commentable->id,
			],
			'subject' => [
				'type' => 'comment',
				'id'   => $comment->id,
				'text' => $this->sanitize($comment->text),
			],
			'actors'  => [
				'id'         => $actor->id,
				'first_name' => $actor->profile->first_name,
				'last_name'  => $actor->profile->last_name,
				'full_name'  => $actor->profile->full_name,
				'avatar'     => $actor->profile->avatar_url,
			],
			'referer' => $this->referer,
		];

		if ($commentableType === 'qna_answer') $this->addQnaAnswerContext();
	}

	public function addQnaAnswerContext()
	{
		$qnaAnswer = $this->comment->commentable;
		$screen = $qnaAnswer->question->screen;
		if (!$screen) return false;

		$lesson = $qnaAnswer->question->screen->lesson;

		$this->data['context'] = [
			'screenId' => $screen->id,
			'lessonId' => $lesson->id,
		];

		return true;
	}
}
