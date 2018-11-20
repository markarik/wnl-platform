<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\UserSubscription;


class UserSubscriptionTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(UserSubscription $subscription)
	{
		$data = [
			'id' => $subscription->id,
			'start_date' => $subscription->start_date->timestamp,
			'end_date' => $subscription->end_date->timestamp,
			'created_at' => $subscription->created_at->timestamp,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
