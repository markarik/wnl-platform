<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CommentPosted
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $comment;

	/**
	 * Create a new event instance.
	 *
	 * @param Comment $comment
	 */
	public function __construct(Comment $comment)
	{
		$this->comment = $comment;
	}

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return Channel|array
	 */
	public function broadcastOn()
	{
		return new PrivateChannel('channel-name');
	}

	public function transform()
	{
		$comment = $this->comment;

		$this->data = [
			'event'   => 'comment-posted',
			'objects' => [
				'type' => snake_case(class_basename($comment->commentable)),
				'id'   => $comment->commentable->id,
			],
		];

		if ($actor = $comment->commentable->user) {
			$this->data['actors'] = [
				'id'         => $actor->id,
				'first_name' => $actor->first_name,
				'last_name'  => $actor->last_name,
				'full_name'  => $actor->full_name,
			];
		}

	}
}
