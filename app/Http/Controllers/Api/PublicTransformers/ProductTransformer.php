<?php


namespace App\Http\Controllers\Api\PublicTransformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\Product;

class ProductTransformer extends ApiTransformer
{

	public function transform(Product $product)
	{
		$data = [
			'id'            => $product->id,
			'name'          => $product->name,
			'slug'          => $product->slug,
			'price'         => $product->price,
			'quantity'      => $product->quantity,
			'initial'       => $product->initial,
			'course_start'  => $product->course_start->timestamp ?? null,
			'course_end'    => $product->course_end->timestamp ?? null,
			'access_start'  => $product->access_start->timestamp ?? null,
			'access_end'    => $product->access_end->timestamp ?? null,
			'signups_start' => $product->signups_start->timestamp ?? null,
			'signups_end'   => $product->signups_end->timestamp ?? null,
			'signups_close' => $product->signups_close->timestamp ?? null,
		];

		$signupsOpen = false;
		if ($product->signups_start && $product->signups_close) {
			$signupsOpen = $product->signups_start->isPast() && $product->signups_close->isFuture();
		}

		$data = array_merge($data, ['signups_open' => $signupsOpen]);

		return $data;
	}
}
