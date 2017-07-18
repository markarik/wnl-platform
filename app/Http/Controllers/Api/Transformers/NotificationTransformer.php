<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Notification;
use App\Http\Controllers\Api\ApiTransformer;

class NotificationTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(Notification $notification)
	{
		$data = [
			'id'      => $notification->id,
			'read_at' => $notification->read_at->timestamp ?? null,
			'seen_at' => $notification->seen_at->timestamp ?? null,
			'channel' => $notification->channel,
		];

		$data = array_merge($data, $notification->data);

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
