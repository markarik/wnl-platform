<?php

namespace App\Http\Requests\Payment;

use App\Models\Order;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Auth;

class PostPaymentMethod extends FormRequest
{
	public function authorize()
	{
		$order = Order::find($this->request->get('order_id'));

		return Auth::user()->can('update', $order);
	}

	public function rules()
	{
		return [
			'order_id' => 'required|exists:orders,id',
			'payment' => [
				'required',
				Rule::in([
					'free',
					'instalments',
					'online',
				])
			],
		];
	}
}
