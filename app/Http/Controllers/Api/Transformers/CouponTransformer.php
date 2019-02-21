<?php

namespace App\Http\Controllers\Api\Transformers;

use App\Http\Controllers\Api\ApiTransformer;
use App\Models\Coupon;

class CouponTransformer extends ApiTransformer
{
	public function transform(Coupon $coupon)
	{
		$data = [
			'id' => $coupon->id,
			'name' => $coupon->name,
			'slug' => $coupon->slug,
			'code' => $coupon->code,
			'type' => $coupon->type,
			'value' => $coupon->value,
			'expires_at' => $coupon->expires_at,
			'user_id' => $coupon->user_id,
			'times_usable' => $coupon->times_usable
		];

		return $data;
	}
}
