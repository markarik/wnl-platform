<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Product;
use App\Http\Controllers\Api\ApiTransformer;

class ProductsTransformer extends ApiTransformer
{
	protected $availableIncludes = [];

	public function transform(Product $product)
	{
		return [
			'id' => $product->id,
		];
	}

}
