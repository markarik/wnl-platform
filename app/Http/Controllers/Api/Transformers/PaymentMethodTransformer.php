<?php

namespace App\Http\Controllers\Api\Transformers;

use App\Http\Controllers\Api\ApiTransformer;
use App\Models\PaymentMethod;

class PaymentMethodTransformer extends ApiTransformer
{
	public function __construct($parent = [])
	{
		$this->parent = $parent;
	}

	public function transform(PaymentMethod $method)
	{
		$data = [
			'id'         => $method->id,
			'slug'       => $method->slug,
			'updated_at' => $method->updated_at->timestamp ?? null,
			'created_at' => $method->created_at->timestamp ?? null,
		];

		return $data;
	}
}
