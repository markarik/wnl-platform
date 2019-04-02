<?php

namespace App\Http\Requests\Coupon;

use App\Models\Coupon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SyncCoupon extends FormRequest
{
	public function authorize(Request $request)
	{
		if (empty(config('coupons.coupons_sync_token'))) {
			return false;
		}

		return $request->header(config('coupons.coupons_sync_header')) === config('coupons.coupons_sync_token');
	}

	public function rules() {
		return [
			'coupon.code' => 'string|required',
			'coupon.type' => 'string|required',
			'coupon.value' => 'required',
			'coupon.kind' => [
				'required',
				Rule::in([
					Coupon::KIND_GROUP,
					Coupon::KIND_PARTICIPANT,
					Coupon::KIND_STUDY_BUDDY,
					Coupon::KIND_VOUCHER,
				])
			]
		];
	}
}
