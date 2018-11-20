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
			'access_start' => $subscription->access_start->timestamp ?? null,
			'access_end' => $subscription->access_end->timestamp ?? null,
			'created_at' => $subscription->created_at->timestamp ?? null,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
