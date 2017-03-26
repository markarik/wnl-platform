<?php


namespace Lib\Invoice;


use App\Models\Order;
use App\Models\User;
use Facades\Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Invoice
{
	const PROFORMA_SERIES_NAME = 'PROFORMA';

	const ADVANCE_SERIES_NAME = 'F-ZAL';

	const FINAL_SERIES_NAME = 'FK';

	const VAT_THRESHOLD = 160000;

	public function issueFromOrder(Order $order, $proforma = false)
	{
		if ($proforma) {
			$this->proforma($order);
		} else {
			$this->advance($order);
		}
	}

	public function proforma(Order $order)
	{
		$invoice = $order->invoices()->create([
			'number' => $this->nextNumberInSeries(self::PROFORMA_SERIES_NAME),
			'series' => self::PROFORMA_SERIES_NAME,
		]);

		$data = [
			'invoiceData' => [
				'id' => $invoice->id,
				'full_number' => $invoice->full_number,
				'date' => $invoice->created_at->formatLocalized('%d %B %Y'),
				'payment_date' => $invoice->created_at->addDays(7)->formatLocalized('%d %B %Y'),
				'payment_method' => 'przelew',
			],
		];

		$data['buyer'] = $this->getBuyerData($order->user);

		$data['ordersList'] = [
			[
				'product_name' => $order->product->name,
				'unit' => 'szt.',
				'amount' => 1,
				'price' => $order->product->price,
			],
		];
		$totalPrice = $order->product->price;

		if ($order->coupon) {
			$data['coupon'] = [
				'value' => $order->coupon_amount,
				'total_with_coupon' => $order->total_with_coupon,
			];
			$totalPrice = $order->total_with_coupon;
		}

		$data['notes'] = [
			'order_number' => $order->id,
		];

		$data['summary'] = [
			'total' => $totalPrice,
		];

		$this->renderAndSave('payment.invoices.pro-forma', $data);
	}

	public function advance(Order $order)
	{
		$invoice = $order->invoices()->create([
			'number' => $this->nextNumberInSeries(self::ADVANCE_SERIES_NAME),
			'series' => self::ADVANCE_SERIES_NAME,
		]);

		$data = [
			'full_number' => $invoice->full_number,
			'invoice_id'  => $invoice->id,
			'name'        => $order->user->full_name,
		];

		$this->renderAndSave('payment.invoices.advance', $data);
	}

	public function finalInvoice(Order $order)
	{

	}

	protected function getBuyerData(User $user)
	{
		if ($user->invoice) {
			return [
				'name' => $user->invoice_name,
				'address' => $user->invoice_address,
				'zip' => $user->invoice_zip,
				'city' => $user->invoice_city,
				'country' => $user->invoice_country,
				'nip' => 'NIP: ' . $user->invoice_nip,
			];
		} else {
			return [
				'name' => $user->full_name,
				'address' => $user->address,
				'zip' => $user->zip,
				'city' => $user->city,
				'country' => '',
				'nip' => '',
			];
		}
	}

	protected function renderAndSave($viewName, $data)
	{
		$view = view($viewName, $data);

		$html = $view->render();

		$pdf = PDF::loadHtml($html)->setPaper('a4');

		Storage::put("invoices/{$data['invoiceData']['id']}.pdf", $pdf->stream());
	}

	protected function nextNumberInSeries($series)
	{
		$dbResult = DB::table('invoices')
			->select('number')
			->where('series', $series)
			->max('number');

		if ($dbResult === null) {
			return 1;
		}

		return $dbResult;
	}
}
