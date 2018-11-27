<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Coupon;
use App\Http\Controllers\Api\ApiTransformer;

class CouponsTransformer extends ApiTransformer
{
	protected $availableIncludes = [];
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(Coupon $coupon)
	{
		$data = [
			'id' => $coupon->id,
			'name' => $coupon->name,
			'slug' => $coupon->slug,
			'code' => $coupon->code,
			'type' => $coupon->type,
			'value' => $coupon->value,
			'expires_at' => $coupon->expires_at->timestamp,
			'user_id' => $coupon->user_id,
			'times_usable' => $coupon->times_usable,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}

}
