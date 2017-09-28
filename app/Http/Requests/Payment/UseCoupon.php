<?php

namespace App\Http\Requests\Payment;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UseCoupon extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'code' => 'required|alpha_num',
		];
	}

	public function withValidator($validator)
	{
		$code = $this->request->get('code');
		if (!$code) return;

		$code = strtoupper($code);
		$coupon = $this->validateVoucher($code);
		$validator->after(function ($validator) use ($coupon) {
			if (!$coupon) {
				$validator->errors()->add(
					'code',
					trans('payment.voucher-is-invalid')
				);
			}
		});
	}

	protected function validateVoucher($code)
	{
		$coupon = Coupon::select()
			->where('code', $code)
			->where(function ($query) {
				$query
					->where('times_usable', '>', 0)
					->orWhere('times_usable', null);
			})
			->where('expires_at', '>', Carbon::now())
			->first();

		if (!$coupon) return false;

		return $coupon;
	}
}
