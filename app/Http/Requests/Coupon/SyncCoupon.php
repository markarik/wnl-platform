<?php

namespace App\Http\Requests\Coupon;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class SyncCoupon extends FormRequest
{
	public function authorize(Request $request)
	{
		return $request->header(config('coupons.coupons_sync_header')) === config('coupons.coupons_sync_token');
	}

	public function rules() {
		return [
			'coupon.code' => 'string|required',
			'coupon.type' => 'string|required',
			'coupon.value' => 'required'
		];
	}
}
