<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Payment;
use App\Http\Controllers\Api\ApiTransformer;

class PaymentTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parentData = [])
	{
		$this->parent = $parentData;
	}

	public function transform(Payment $payment)
	{
		$orderId = $this->parent['order_id'];

		$data = [
			'id' => $payment->id,
			'status' => $payment->status,
			'external_id' => $payment->external_id,
			'orders' => $orderId,
			'created_at' => $payment->created_at->format('d-m-Y H:i:s'),
			'amount' => $payment->amount,
		];

		return $data;
	}
}
