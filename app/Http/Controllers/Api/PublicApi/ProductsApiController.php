<?php


namespace App\Http\Controllers\Api\PublicApi;


use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\PublicTransformers\ProductTransformer;
use App\Models\Product;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;

class ProductsApiController extends ApiController
{
	/**
	 * ProductsApiController constructor.
	 * @param Request $request
	 */
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.products');
	}

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
