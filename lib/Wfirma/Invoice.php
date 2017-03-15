<?php


namespace Lib\Wfirma;


use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Invoice extends Client
{
	public function __construct()
	{
		parent::__construct();
		$this->resource = 'invoices';
	}

	public function issueFromOrder(Order $order, $proforma = false)
	{
		$type = 'receipt_normal';
		$date = Carbon::now()->format('Y-m-d');
		$products = $this->invoiceContents($order);
		$contractor = [];
		$user = $order->user;

		if ($user->invoice) {
			$contractor = $this->contractor($user);
			$type = $proforma ? 'proforma' : 'normal';
		}

		$invoiceData = [
			'id_external'         => $order->id,
			'type'                => $type,
			'disposaldate'        => $date,
			'paymentdate'         => $date,
			'paymentdate_presets' => $date,
			'paymentmethod'       => 'transfer',
			'alreadypaid_initial' => $order->total_with_coupon,
			'contractor'          => $contractor,
			'invoicecontents'     => $products,
			'description'         => 'Zamówienie #' . $order->id,
			'price_type'          => 'brutto',
		];

		$invoice = $this->add($invoiceData);

		Storage::put("invoices/{$invoice['id']}.pdf", $this->download($invoice['id']));

		$order->invoices()->create([
			'external_id' => $invoice['id'],
			'full_number' => $invoice['fullnumber'],
		]);
	}

	protected function invoiceContents($order)
	{
		$data['invoicecontents'][0] = [
			'invoicecontent' => [
				'name'  => $order->product->name,
				'count' => 1,
				'price' => $order->total_with_coupon,
				'vat'   => '23%',
				'code'  => $order->product->slug,
				'unit'  => 'szt.',
			],
		];

		return $data;
	}

	protected function contractor($user)
	{
		$data['contractor'] = [
			'name'    => $user->invoice_name,
			'street'  => $user->invoice_address,
			'zip'     => $user->invoice_zip,
			'city'    => $user->invoice_city,
			'country' => $user->invoice_country,
			'nip'     => $user->invoice_nip,
		];

		return $data;
	}
}