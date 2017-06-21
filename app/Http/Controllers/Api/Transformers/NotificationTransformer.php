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

	public function transform(Notification $address)
	{
		$data = [
			'data'    => $address->data,
			'read_at' => $address->read_at->timestamp ?? null,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
