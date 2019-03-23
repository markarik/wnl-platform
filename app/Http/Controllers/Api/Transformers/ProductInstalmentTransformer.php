<?php

namespace App\Http\Controllers\Api\Transformers;

use App\Http\Controllers\Api\ApiTransformer;
use App\Models\ProductInstalment;

class ProductInstalmentTransformer extends ApiTransformer
{
	private $parent;

	public function __construct($parent = [])
	{
		$this->parent = $parent;
	}

	public function transform(ProductInstalment $instalment)
	{
		$data = [
			'id'           => $instalment->id,
			'product_id'   => $instalment->product_id,
			'value_type'   => $instalment->value_type,
			'value'        => $instalment->value,
			'due_date'     => $instalment->due_date->timestamp ?? null,
			'due_days'     => $instalment->due_days,
			'order_number' => $instalment->order_number,
			'created_at'   => $instalment->created_at->timestamp ?? null,
			'updated_at'   => $instalment->updated_at->timestamp ?? null,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
