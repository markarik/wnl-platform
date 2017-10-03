<?php

namespace App\Events;

use App\Models\Comment;
use App\Traits\EventContextTrait;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CommentRemoved extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		SanitizesUserContent,
		EventContextTrait;

	public $comment;
	public $action;
	public $userId;

	/**
	 * Create a new event instance.
	 *
	 * @param Comment $comment
	 * @param int $userId
	 */
	public function __construct(Comment $comment, $userId, $action)
	{
		parent::__construct();
		$this->comment = $comment;
		$this->userId = $userId;
		$this->action = $action;
	}

	public function transform()
	{
		$comment = $this->serializedModel();
		$commentable = $comment->commentable;
		$commentableType = snake_case(class_basename($commentable));

		$this->data = [
			'event'   => 'comment-' . $this->action,
			'subject' => [
				'type' => 'comment',
				'id'   => $comment->id,
				'text' => $this->sanitize($comment->text),
			],
			'commentable' => [
				'id' => $commentable->id,
				'type' => $commentableType
			],
			'actors'  => [
				'id' => $this->userId
			],
			'referer' => $this->referer,
			'context' => $this->addEventContext($commentable)
		];
	}

	private function serializedModel() {
		$serializedModel = Comment::find($this->comment->id);

		return !empty($serializedModel) ? $serializedModel : $this->comment;
	}
}
