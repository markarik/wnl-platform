<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Order;
use App\Http\Controllers\Api\ApiTransformer;

class OrderTransformer extends ApiTransformer
{

	public function transform(Order $order)
	{
		$data = [
			'id'          => $order->id,
			'paid'        => $order->paid,
			'paid_amount' => $order->paid_amount,
			'method'      => $order->method,
			'total'       => $order->total_with_coupon,
			'canceled'    => $order->canceled,
			'created_at'  => $order->created_at->format('d-m-Y'),
			'product'     => [
				'id'    => $order->product->id,
				'name'  => $order->product->name,
				'slug'  => $order->product->slug,
				'price' => $order->product->price,
			],
		];

		if ($order->coupon) {
			$data['coupon'] = [
				'id'    => $order->coupon->id,
				'name'  => $order->coupon->name,
				'code'  => $order->coupon->code,
				'type'  => $order->coupon->type,
				'value' => $order->coupon->value,
				'slug'  => $order->coupon->slug,
			];
		}

		if ($order->studyBuddy) [
			$data['studyBuddy'] = [
				'id'        => $order->studyBuddy->id,
				'code'      => $order->studyBuddy->code,
				'recipient' => $order->studyBuddy->recipient,
				'refunded'  => $order->studyBuddy->refunded,
			],
		];

		if (is_null($order->method) || $order->method === 'instalments') {
			$data['instalments'] = $order->instalments;
		}

		return $data;
	}
}
