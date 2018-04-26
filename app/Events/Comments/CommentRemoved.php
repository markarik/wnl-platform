<?php

namespace App\Events\Comments;

use App\Events\Event;
use App\Events\SanitizesUserContent;
use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use App\Traits\EventContextTrait;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class CommentRemoved extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		SanitizesUserContent,
		EventContextTrait;

	public $comment;
	public $action;
	public $userId;
	public $channels;

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
		$this->channels = collect();
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

	public function broadcastOn()
	{
		$commentable = $this->comment->commentable;
		$commentableType = snake_case(class_basename($commentable));
		$this->channels->push(new Channel('comments'));
		$this->channels->push(new Channel(
			"commentable-{$commentableType}-{$commentable->id}"
		));

		return $this->channels->toArray();
	}
}
