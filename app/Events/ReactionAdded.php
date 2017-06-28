<?php

namespace App\Events;

use Request;
use App\Models\User;
use App\Models\Reaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ReactionAdded
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $reaction;

	public $reactable;

	public $userId;

	public $referer;

	/**
	 * Create a new event instance.
	 *
	 * @param Reaction $reaction
	 * @param Model $reactable
	 */
	public function __construct(Reaction $reaction, Model $reactable, $userId)
	{
		$this->reaction = $reaction;
		$this->reactable = $reactable;
		$this->userId = $userId;
		$this->referer = Request::header('X-BETHINK-LOCATION');
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
		$reaction = $this->reaction;
		$reactable = $this->reactable;
		$actor = User::find($this->userId);

		$this->data = [
			'event'   => 'reaction-added',
			'objects' => [
				'type' => snake_case(class_basename($reactable)),
				'id'   => $reactable->id,
			],
			'subject' => [
				'type'          => 'reaction',
				'reaction_type' => $reaction->type,
				'reaction_id'   => $reaction->id,
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
	}
}
