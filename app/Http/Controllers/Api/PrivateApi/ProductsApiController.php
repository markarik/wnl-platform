<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Product\CreateProduct;
use App\Http\Requests\Product\UpdateProduct;
use App\Models\Product;
use Auth;
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
		$productId = $request->route('id');
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

	public function getBySlug($slug)
	{
		$product = Product::slug($slug);

		if(is_null($product)){
			return $this->respondNotFound();
		}

		return $this->transformAndRespond($product);
	}

	public function getPaidCourseForUser()
	{
		$user = Auth::user();

		if (is_null($user)) {
			return $this->respondNotFound();
		}

		$productId = $user->getLatestPaidCourseProductId();

		if (is_null($productId)) {
			return $this->respondNotFound();
		}

		return $this->transformAndRespond(Product::findOrFail($productId));
	}

	public function getVatRates()
	{
		return $this->respondOk(['vat_rates' => Product::VAT_RATES]);
	}

	public function syncPaymentMethods(Product $product, Request $request)
	{
		$data = collect($request->payment_methods)->map(function ($item) {
			return [
				'payment_method_id' => $item['id'],
				'start_date'        => $this->getDate($item['start_date']),
				'end_date'          => $this->getDate($item['end_date']),
			];
		})->keyBy('payment_method_id');
		$product->paymentMethods()->sync($data);
	}

	public function syncInstalments(Product $product, Request $request)
	{
		foreach ($request->instalments as $instalment) {
			$product->instalments()->updateOrCreate(
				['order_number' => $instalment['order_number']],
				[
					'due_date'     => $this->getDate($instalment['due_date']),
					'order_number' => $instalment['order_number'],
					'product_id'   => $instalment['product_id'],
					'value_type'   => $instalment['value_type'],
					'value'        => $instalment['value'],
					'due_days'     => $instalment['due_days'],
				]
			);
		}
	}

	public function getCurrent()
	{
		return $this->transformAndRespond(Product::whereNotNull('slug')->get());
	}

	private function transformRequestParams($request)
	{
		return [
			'name'          => $request->name,
			'invoice_name'  => $request->invoice_name,
			'slug'          => $request->slug ? $request->slug : null,
			'price'         => $request->price,
			'quantity'      => $request->quantity,
			'initial'       => $request->initial,
			'delivery_date' => $this->getDate($request->delivery_date),
			'course_start'  => $this->getDate($request->course_start),
			'course_end'    => $this->getDate($request->course_end),
			'access_start'  => $this->getDate($request->access_start),
			'access_end'    => $this->getDate($request->access_end),
			'signups_start' => $this->getDate($request->signups_start),
			'signups_end'   => $this->getDate($request->signups_end),
			'signups_close' => $this->getDate($request->signups_close),
			'vat_rate'      => $request->vat_rate,
			'vat_note'      => $request->vat_note,
		];
	}

	private function getDate($timestamp)
	{
		if (!$timestamp) {
			return null;
		}

		return Carbon::createFromTimestamp((int)$timestamp);
	}
}
