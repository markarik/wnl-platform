<?php

namespace App\Http\Requests\Payment;

use App\Models\Coupon;
use Carbon\Carbon;
use GrahamCampbell\Throttle\Facades\Throttle;
use Illuminate\Foundation\Http\FormRequest;
use Auth;

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
			'code' => 'required',
		];
	}

	public function getValidator()
	{
		return $this->getValidatorInstance();
	}

	public function withValidator($validator)
	{
		$code = $this->request->get('code');
		if (!$code) return;

		$validator->after(function ($validator) use ($code) {

			$limitReached = $this->limitReached();
			$coupon = $this->validateVoucher(mb_convert_case($code, MB_CASE_UPPER, "UTF-8"));

			if (!$coupon) {
				$validator->errors()->add(
					'code',
					trans('payment.voucher-is-invalid')
				);
			}

			// Disabled due to load balancer client IP forwarding issue
//			if ($limitReached) {
//				$validator->errors()->add(
//					'code',
//					trans('payment.voucher-tries-limit-reached')
//				);
//			}
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

	protected function limitReached()
	{
		$throttler = Throttle::hit([
			'ip'    => app('request')->server('REMOTE_ADDR'),
			'route' => 'coupon-tries-limit',
		], 10, 60 * 24);

		return !$throttler->check();
	}
}
