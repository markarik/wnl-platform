<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\Product;

class ProductTransformer extends ApiTransformer
{

	protected $availableIncludes = ['payment_methods', 'instalments'];
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(Product $product)
	{
		$data = [
			'id'            => $product->id,
			'name'          => $product->name,
			'invoice_name'  => $product->invoice_name,
			'slug'          => $product->slug,
			'price'         => $product->price,
			'quantity'      => $product->quantity,
			'initial'       => $product->initial,
			'delivery_date' => $product->delivery_date->timestamp ?? null,
			'created_at'    => $product->created_at->timestamp ?? null,
			'updated_at'    => $product->updated_at->timestamp ?? null,
			'course_start'  => $product->course_start->timestamp ?? null,
			'course_end'    => $product->course_end->timestamp ?? null,
			'access_start'  => $product->access_start->timestamp ?? null,
			'access_end'    => $product->access_end->timestamp ?? null,
			'signups_start' => $product->signups_start->timestamp ?? null,
			'signups_end'   => $product->signups_end->timestamp ?? null,
			'signups_close' => $product->signups_close->timestamp ?? null,
			'vat_rate'      => $product->vat_rate,
			'vat_note'      => $product->vat_note,
		];

		$signupsOpen = false;
		if ($product->signups_start && $product->signups_close) {
			$signupsOpen = $product->signups_start->isPast() && $product->signups_close->isFuture();
		}

		$data = array_merge($data, ['signups_open' => $signupsOpen]);

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}

	public function includePaymentMethods(Product $product)
	{
		$paymentMethods = $product->paymentMethods;
		$transformer = new PaymentMethodTransformer(['products' => $product->id]);

		return $this->collection($paymentMethods, $transformer, 'payment_methods');
	}

	public function includeInstalments(Product $product){
		$instalments = $product->instalments;
		$transformer = new ProductInstalmentTransformer(['products' => $product->id]);
		return $this->collection($instalments, $transformer, 'instalments');
	}
}
