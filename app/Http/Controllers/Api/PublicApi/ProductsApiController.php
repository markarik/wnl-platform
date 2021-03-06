<?php


namespace App\Http\Controllers\Api\PublicApi;


use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\PublicTransformers\ProductTransformer;
use App\Models\Product;
use League\Fractal\Resource\Collection;

class ProductsApiController extends ApiController
{
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getCurrent()
	{
		$products = Product::select()
			->whereNotNull('slug')
			->get();

		$transformer = new ProductTransformer;
		$resource = new Collection($products, $transformer, $this->resourceName);
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}
}
