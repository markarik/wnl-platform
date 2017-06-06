<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Coupon;
use App\Http\Controllers\Api\ApiTransformer;

class CouponsTransformer extends ApiTransformer
{
	protected $availableIncludes = [];

	public function transform(Coupon $coupon)
	{
		return [
			'id' => $coupon->id,
		];
	}

}
