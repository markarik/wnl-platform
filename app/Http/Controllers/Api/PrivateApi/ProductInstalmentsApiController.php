<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Models\Product;
use Illuminate\Http\Request;

class AnnotationsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.product-instalments');
	}

	public function getByProduct(Product $product)
	{
		return $this->transformAndRespond($product->instalments);
	}
}
