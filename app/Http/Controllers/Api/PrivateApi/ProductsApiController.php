<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Product\CreateProduct;
use App\Http\Requests\Product\UpdateProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProductsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.products');
	}

	public function put(UpdateProduct $request)
	{
		$productId = $request->get('id');
		$product = Product::find($productId);

		if (is_null($product)) {
			return $this->respondNotFound();
		}

		$product->update($this->transformRequestParams($request));

		return $this->transformAndRespond($product);
	}

	public function post(CreateProduct $request)
	{
		$product = Product::create($this->transformRequestParams($request));

		return $this->transformAndRespond($product);
	}

	public function getVatRates()
	{
		return $this->respondOk(['vat_rates' => Product::VAT_RATES]);
	}

	private function transformRequestParams($request) {
		return [
			'name'          => $request->name,
			'invoice_name'  => $request->invoice_name,
			'slug'          => $request->slug,
			'price'         => $request->price,
			'quantity'      => $request->quantity,
			'initial'       => $request->initial,
			'delivery_date' => Carbon::createFromTimestamp($request->delivery_date),
			'course_start'  => Carbon::createFromTimestamp($request->course_start),
			'course_end'    => Carbon::createFromTimestamp($request->course_end),
			'access_start'  => Carbon::createFromTimestamp($request->access_start),
			'access_end'    => Carbon::createFromTimestamp($request->access_end),
			'signups_start' => Carbon::createFromTimestamp($request->signups_start),
			'signups_end'   => Carbon::createFromTimestamp($request->signups_end),
			'signups_close' => Carbon::createFromTimestamp($request->signups_close),
			'vat_rate'      => $request->vat_rate,
			'vat_note'      => $request->vat_note,
		];
	}
}
