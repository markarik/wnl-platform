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
	const VAT_THRESHOLD = 159452.00;
	const VAT_ZERO = 0;
	const VAT_NORMAL = 0.23;
	const DAYS_FOR_PAYMENT = 7;

	public function issueFromOrder(Order $order, $proforma = false)
	{
		if ($proforma) {
			return $this->proforma($order);
		} else {
			return $this->advance($order);
		}
	}

	public function proforma(Order $order)
	{
		$invoice = $order->invoices()->create([
			'number' => $this->nextNumberInSeries(self::PROFORMA_SERIES_NAME),
			'series' => self::PROFORMA_SERIES_NAME,
		]);

		$data = [
			'notes' => [],
			'invoiceData' => [
				'id' => $invoice->id,
				'full_number' => $invoice->full_number,
				'date' => $invoice->created_at->format('d.m.Y'),
				'payment_date' => $invoice->created_at->addDays(self::DAYS_FOR_PAYMENT)->format('d.m.Y'),
				'payment_method' => 'przelew',
			],
		];

		$data['buyer'] = $this->getBuyerData($order->user);

		$data['ordersList'] = [
			[
				'product_name' => $order->product->name,
				'unit' => 'szt.',
				'amount' => 1,
			],
		];
		$totalPrice = $order->product->price;

		if ($order->coupon) {
			$data['coupon'] = [
				'value' => $order->coupon_amount,
				'total_with_coupon' => $order->total_with_coupon,
			];
			$totalPrice = $order->total_with_coupon;
			$data['notes'][] = 'Cena obniżona na podstawie kuponu Zniżka 200zł dla subskrybentów.';
		}

		// Calculate netto, brutto, VAT
		$vatValue = $this->getVatValue();
		$data['ordersList'][0]['priceGross'] = number_format($totalPrice, 2);
		$data['ordersList'][0]['priceNet'] = number_format($totalPrice / (1 + $vatValue), 2);
		$data['ordersList'][0]['vat'] = $this->getVatString($vatValue);

		$data['summary'] = [
			'total' => number_format($totalPrice, 2),
		];

		$data['notes'][] = sprintf('Zamówienie nr %d', $order->id);
		if ($vatValue === self::VAT_ZERO) {
			$data['notes'][] = 'Zwolnienie z VAT na podstawie art. 113 ust. 1 Ustawy z dnia 11 marca 2004r. o podatku od towarów i usług';
		}

		$this->renderAndSave('payment.invoices.pro-forma', $data);

		return $invoice;
	}

	public function advance(Order $order)
	{
		$invoice = $order->invoices()->create([
			'number' => $this->nextNumberInSeries(self::ADVANCE_SERIES_NAME),
			'series' => self::ADVANCE_SERIES_NAME,
		]);

		$data = [
			'notes' => [],
			'invoiceData' => [
				'id' => $invoice->id,
				'full_number' => $invoice->full_number,
				'date' => $invoice->created_at->format('d.m.Y'),
				'payment_date' => $invoice->created_at->format('d.m.Y'),
				'payment_method' => 'przelew',
			],
		];

		$data['buyer'] = $this->getBuyerData($order->user);

		$data['ordersList'] = [
			[
				'product_name' => $order->product->name,
				'unit' => 'szt.',
				'amount' => 1,
			],
		];
		$totalPrice = $order->product->price;

		if ($order->coupon) {
			$data['coupon'] = [
				'value' => $order->coupon_amount,
				'total_with_coupon' => $order->total_with_coupon,
			];
			$totalPrice = $order->total_with_coupon;
			$data['notes'][] = 'Cena obniżona na podstawie kuponu Zniżka 200zł dla subskrybentów.';
		}

		// Calculate netto, brutto, VAT
		$vatValue = $this->getVatValue();
		$data['ordersList'][0]['priceGross'] = number_format($totalPrice, 2);
		$data['ordersList'][0]['priceNet'] = number_format($totalPrice / (1 + $vatValue), 2);
		$data['ordersList'][0]['vat'] = $this->getVatString($vatValue);
		$data['ordersList'][0]['vatValue'] = number_format($vatValue * $totalPrice / (1 + $vatValue));

		$data['summary'] = [
			'total' => number_format($totalPrice, 2),
		];

		$data['notes'][] = sprintf('Zamówienie nr %d', $order->id);
		if ($vatValue === self::VAT_ZERO) {
			$data['notes'][] = 'Zwolnienie z VAT na podstawie art. 113 ust. 1 Ustawy z dnia 11 marca 2004r. o podatku od towarów i usług';
		}

		$this->renderAndSave('payment.invoices.advance', $data);

		return $invoice;
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

		// Best hack ever! xD
		$html = iconv('UTF-8', 'UTF-8', $view->render());

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

	private function getVatValue() {
		if ($this->advanceInvoiceSum() < self::VAT_THRESHOLD) {
			return self::VAT_ZERO;
		}
		return self::VAT_NORMAL;
	}

	private function getVatString($value) {
		if ($value === self::VAT_ZERO) {
			return 'zw.';
		}
		return sprintf('%d%%', $value * 100);
	}

	private function advanceInvoiceSum()
	{
		$orders = Order::whereHas('invoices', function ($query) {
			$query->where('series', self::ADVANCE_SERIES_NAME);
		})->get();

		return $orders->sum('total_with_coupon');
	}
}
