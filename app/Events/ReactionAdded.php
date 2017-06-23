<?php

namespace App\Events;

use App\Models\Reaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ReactionAdded
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $reaction;

	public $reactable;

	/**
	 * Create a new event instance.
	 *
	 * @param Reaction $reaction
	 * @param Model $reactable
	 */
	public function __construct(Reaction $reaction, Model $reactable)
	{
		$this->reaction = $reaction;
		$this->reactable = $reactable;
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
		];
	}
}
