<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Invoice;
use App\Http\Controllers\Api\ApiTransformer;

class InvoiceTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parentData = [])
	{
		$this->parent = $parentData;
	}

	public function transform(Invoice $invoice)
	{
		$orderId = $this->parent['order_id'];

		return [
			'id' => $invoice->id,
			'created_at' => $invoice->created_at,
			'orders' => $orderId,
			'amount' => $invoice->amount,
			'number' => $invoice->full_number,
			'file' => $invoice->file_path
		];
	}
}
