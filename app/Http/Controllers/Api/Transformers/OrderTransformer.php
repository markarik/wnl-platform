<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Order;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{

	public function transform(Order $order)
	{
		$data = [
			'id'         => $order->id,
			'paid'       => $order->paid,
			'method'     => $order->method,
			'total'      => $order->total_with_coupon,
			'canceled'   => $order->canceled,
			'created_at' => $order->created_at->format('d-m-Y'),
			'product'    => [
				'id'     => $order->product->id,
				'name'   => $order->product->name,
				'slug'   => $order->product->slug,
				'price'  => $order->product->price,
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

		return $data;
	}
}
