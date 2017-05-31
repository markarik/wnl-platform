<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Notification;
use App\Models\UserAddress;
use League\Fractal\TransformerAbstract;

class NotificationTransformer extends TransformerAbstract
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
			'read_at' => $address->read_at,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
