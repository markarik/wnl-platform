<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Product;
use League\Fractal\TransformerAbstract;

class ProductsTransformer extends TransformerAbstract
{
	protected $availableIncludes = [];

	public function transform(Product $product)
	{
		return [
			'id' => $product->id,
		];
	}

}
