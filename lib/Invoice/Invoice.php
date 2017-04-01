<?php


namespace Lib\Invoice;


use App\Models\Order;
use App\Models\User;
use Barryvdh\DomPDF\PDF;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Http\Response;


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
				'product_name' => $order->product->invoice_name,
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
		$data['ordersList'][0]['priceGross'] = $this->price($totalPrice);
		$data['ordersList'][0]['priceNet'] = $this->price($totalPrice / (1 + $vatValue));
		$data['ordersList'][0]['vat'] = $this->getVatString($vatValue);

		$data['summary'] = [
			'total' => $this->price($totalPrice),
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
				'product_name' => $order->product->invoice_name,
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
		$data['ordersList'][0]['priceGross'] = $this->price($totalPrice);
		$data['ordersList'][0]['priceNet'] = $this->price($totalPrice / (1 + $vatValue));
		$data['ordersList'][0]['vat'] = $this->getVatString($vatValue);

		if ($vatValue === self::VAT_ZERO) {
			$data['ordersList'][0]['vatValue'] = '-';
		} else {
			$data['ordersList'][0]['vatValue'] = $this->price($vatValue * $totalPrice / (1 + $vatValue)) . 'zł';
		}

		$data['summary'] = [
			'total' => $this->price($totalPrice),
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

		$config = app(ConfigRepository::class);
		$files = app(Filesystem::class);
		$view = app(ViewFactory::class);
		$options = app()->make('dompdf.options');
		$domPdf = new Dompdf($options);
		$domPdf->setBasePath(realpath(base_path('public')));

		$pdf = new PDF($domPdf, $config, $files, $view);
		$pdf->loadHtml($html);
		$pdf->setPaper('a4');

		Storage::put("invoices/{$data['invoiceData']['id']}.pdf", $pdf->output());
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

		return $dbResult + 1;
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

	private function price($number) {
		return number_format($number, 2, ',', ' ');
	}
}
