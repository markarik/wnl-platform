<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Coupon;
use League\Fractal\TransformerAbstract;

class CouponsTransformer extends TransformerAbstract
{
	protected $availableIncludes = [];

	public function transform(Coupon $coupon)
	{
		return [
			'id' => $coupon->id,
		];
	}

}
