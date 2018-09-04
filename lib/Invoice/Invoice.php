<?php


namespace Lib\Invoice;


use App\Models\Invoice as InvoiceModel;
use App\Models\Order;
use App\Models\User;
use Barryvdh\DomPDF\PDF;
use Dompdf\Dompdf;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Invoice
{
	const PROFORMA_SERIES_NAME = 'PROFORMA';
	const ADVANCE_SERIES_NAME = 'F-ZAL';
	const FINAL_SERIES_NAME = 'FK';
	const VAT_SERIES_NAME = 'FV';
	const CORRECTIVE_SERIES_NAME = 'KOR';
	const VAT_THRESHOLD = 159452.00;
	const VAT_ZERO = 0;
	const VAT_NORMAL = 0.23;
	const DAYS_FOR_PAYMENT = 7;

	public function vatInvoice(Order $order, $invoice = null)
	{
		$vatValue = $this->getVatValue($order->paid_amount);
		$vatString = $this->getVatString($vatValue);
		if (!$invoice) {
			$invoice = $order->invoices()->create([
				'number' => $this->nextNumberInSeries(self::VAT_SERIES_NAME),
				'series' => self::VAT_SERIES_NAME,
				'amount' => $order->paid_amount,
				'vat'    => $vatValue === self::VAT_ZERO ? 'zw' : '23',
			]);
		}

		$data = [
			'notes'       => [],
			'invoiceData' => [
				'id'             => $invoice->id,
				'full_number'    => $invoice->full_number,
				'date'           => $invoice->created_at->format('d.m.Y'),
				'payment_date'   => $invoice->created_at->format('d.m.Y'),
				'payment_method' => 'przelew',
			],
		];

		$data['buyer'] = $this->getBuyerData($order->user);

		$data['ordersList'] = [
			[
				'product_name' => $order->product->invoice_name,
				'unit'         => 'szt.',
				'amount'       => 1,
			],
		];
		$totalPrice = $order->product->price;

		if ($coupon = $order->coupon) {
			$data['coupon'] = [
				'value'             => $order->coupon_amount,
				'total_with_coupon' => $order->total_with_coupon,
			];
			$totalPrice = $order->total_with_coupon;
			$data['notes'][] = "Cena obniżona na podstawie kuponu {$coupon->name}.";
		}

		// Calculate netto, brutto, VAT
		$data['ordersList'][0]['priceGross'] = $this->price($totalPrice);
		$data['ordersList'][0]['priceNet'] = $this->price($totalPrice / (1 + $vatValue));
		$data['ordersList'][0]['vat'] = $vatString;

		$data['settlement'] = [
			'priceNet'   => $this->price($order->paid_amount / (1 + $vatValue)),
			'vatValue'   => $this->price($order->paid_amount - $order->paid_amount / (1 + $vatValue)),
			'priceGross' => $this->price($order->paid_amount),
		];

		$data['remainingAmount'] = $this->price($totalPrice - $order->paid_amount);

		$data['recentSettlement'] = $order->paid_amount;

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

		$this->renderAndSave('payment.invoices.vat', $data, $invoice);

		return $invoice;
	}

	public function proforma(Order $order, $invoice = null)
	{
		$vatValue = $this->getVatValue();
		$vatString = $this->getVatString($vatValue);
		if (!$invoice) {
			$invoice = $order->invoices()->create([
				'number' => $this->nextNumberInSeries(self::PROFORMA_SERIES_NAME),
				'series' => self::PROFORMA_SERIES_NAME,
				'amount' => $order->total_with_coupon,
				'vat'    => $vatValue === self::VAT_ZERO ? 'zw' : '23',
			]);
		}

		$data = [
			'notes'       => [],
			'invoiceData' => [
				'id'             => $invoice->id,
				'full_number'    => $invoice->full_number,
				'date'           => $invoice->created_at->format('d.m.Y'),
				'payment_date'   => $invoice->created_at->addDays(self::DAYS_FOR_PAYMENT)->format('d.m.Y'),
				'payment_method' => 'przelew',
			],
		];

		$data['buyer'] = $this->getBuyerData($order->user);

		$data['ordersList'] = [
			[
				'product_name' => $order->product->invoice_name,
				'unit'         => 'szt.',
				'amount'       => 1,
			],
		];
		$totalPrice = $order->product->price;

		if ($coupon = $order->coupon) {
			$data['coupon'] = [
				'value'             => $order->coupon_amount,
				'total_with_coupon' => $order->total_with_coupon,
			];
			$totalPrice = $order->total_with_coupon;
			$data['notes'][] = "Cena obniżona na podstawie kuponu {$coupon->name}.";
		}

		// Calculate netto, brutto, VAT
		$data['ordersList'][0]['priceGross'] = $this->price($totalPrice);
		$data['ordersList'][0]['priceNet'] = $this->price($totalPrice / (1 + $vatValue));
		$data['ordersList'][0]['vat'] = $this->getVatString($vatValue);

		$data['settlement'][0]['priceGross'] = $this->price($totalPrice);
		$data['settlement'][0]['priceNet'] = $this->price($totalPrice / (1 + $vatValue));
		$data['settlement'][0]['vat'] = $vatString;

		$data['summary'] = [
			'total' => $this->price($totalPrice),
		];

		$data['notes'][] = sprintf('Zamówienie nr %d', $order->id);
		if ($vatValue === self::VAT_ZERO) {
			$data['notes'][] = 'Zwolnienie z VAT na podstawie art. 113 ust. 1 Ustawy z dnia 11 marca 2004r. o podatku od towarów i usług';
		}

		$this->renderAndSave('payment.invoices.pro-forma', $data, $invoice);

		return $invoice;
	}

	public function advance(Order $order, $invoice = null)
	{
		$builder = $order->invoices()->where('series', self::ADVANCE_SERIES_NAME);
		if ($invoice) $builder->where('id', '<', $invoice->id);
		$previousAdvances = $builder->get();
		$recentSettlement = $order->paid_amount - $previousAdvances->sum('corrected_amount');
		$vatValue = $this->getVatValue($recentSettlement);
		$vatString = $this->getVatString($vatValue);
		$totalPaid = $recentSettlement + $previousAdvances->sum('corrected_amount');
		if (!$invoice) {
			$invoice = $order->invoices()->create([
				'number' => $this->nextNumberInSeries(self::ADVANCE_SERIES_NAME),
				'series' => self::ADVANCE_SERIES_NAME,
				'amount' => $recentSettlement,
				'vat'    => $vatValue === self::VAT_ZERO ? 'zw' : '23',
			]);
		}

		$data = [
			'notes'       => [],
			'invoiceData' => [
				'id'             => $invoice->id,
				'full_number'    => $invoice->full_number,
				'date'           => $invoice->created_at->format('d.m.Y'),
				'payment_date'   => $invoice->created_at->format('d.m.Y'),
				'payment_method' => 'przelew',
			],
		];

		$data['buyer'] = $this->getBuyerData($order->user);

		$data['ordersList'] = [
			[
				'product_name' => $order->product->invoice_name,
				'unit'         => 'szt.',
				'amount'       => 1,
			],
		];
		$totalPrice = $order->product->price;

		if ($coupon = $order->coupon) {
			$data['coupon'] = [
				'value'             => $order->coupon_amount,
				'total_with_coupon' => $order->total_with_coupon,
			];
			$totalPrice = $order->total_with_coupon;
			$data['notes'][] = "Cena obniżona na podstawie kuponu {$coupon->name}.";
		}

		// Calculate netto, brutto, VAT
		$data['ordersList'][0]['priceGross'] = $this->price($totalPrice);
		$data['ordersList'][0]['priceNet'] = $this->price($totalPrice / (1 + $vatValue));
		$data['ordersList'][0]['vat'] = $vatString;

		$data['settlement'] = [
			'priceNet'   => $this->price($recentSettlement / (1 + $vatValue)),
			'vatValue'   => $this->price($recentSettlement - $recentSettlement / (1 + $vatValue)),
			'priceGross' => $this->price($recentSettlement),
		];

		$data['remainingAmount'] = $this->price($totalPrice - $totalPaid);

		$data['previousAdvances'] = $previousAdvances;
		$data['recentSettlement'] = $recentSettlement;

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

		$this->renderAndSave('payment.invoices.advance', $data, $invoice);

		return $invoice;
	}

	public function finalInvoice(Order $order, $invoice = null)
	{
		$previousAdvances = $order->invoices()->whereIn('series', [
			self::ADVANCE_SERIES_NAME,
			self::CORRECTIVE_SERIES_NAME,
		])->get();
		$recentSettlement = $order->paid_amount - $previousAdvances->sum('corrected_amount');
		$vatValue = $this->getVatValue($recentSettlement);
		$vatString = $this->getVatString($vatValue);
		$totalPaid = $recentSettlement + $previousAdvances->sum('corrected_amount');
		if (!$invoice) {
			$invoice = $order->invoices()->create([
				'number' => $this->nextNumberInSeries(self::FINAL_SERIES_NAME),
				'series' => self::FINAL_SERIES_NAME,
				'amount' => $recentSettlement,
				'vat'    => $vatValue === self::VAT_ZERO ? 'zw' : '23',
			]);
		}

		$data = [
			'notes'       => [],
			'invoiceData' => [
				'id'             => $invoice->id,
				'full_number'    => $invoice->full_number,
				'date'           => $order->product->delivery_date->format('d.m.Y'),
				'payment_date'   => $invoice->created_at->format('d.m.Y'),
				'payment_method' => 'przelew',
			],
		];

		$data['buyer'] = $this->getBuyerData($order->user);

		$data['ordersList'] = [
			[
				'product_name' => $order->product->invoice_name,
				'unit'         => 'szt.',
				'amount'       => 1,
			],
		];
		$totalPrice = $order->product->price;

		if ($coupon = $order->coupon) {
			$data['coupon'] = [
				'value'             => $order->coupon_amount,
				'total_with_coupon' => $order->total_with_coupon,
			];
			$totalPrice = $order->total_with_coupon;
			$data['notes'][] = "Cena obniżona na podstawie kuponu {$coupon->name}.";
		}

		// Calculate netto, brutto, VAT
		$data['ordersList'][0]['priceGross'] = $this->price($totalPrice);
		$data['ordersList'][0]['priceNet'] = $this->price($totalPrice / (1 + $vatValue));
		$data['ordersList'][0]['vat'] = $vatString;

		$data['settlement'] = [
			'priceNet'   => $this->price($recentSettlement / (1 + $vatValue)),
			'vatValue'   => $this->price($recentSettlement - $recentSettlement / (1 + $vatValue)),
			'priceGross' => $this->price($recentSettlement),
		];

		$data['remainingAmount'] = $this->price($totalPrice - $previousAdvances->sum('corrected_amount'));

		$data['previousAdvances'] = $previousAdvances;
		$data['recentSettlement'] = $recentSettlement;

		if ($vatValue === self::VAT_ZERO) {
			$data['ordersList'][0]['vatValue'] = '-';
		} else {
			$data['ordersList'][0]['vatValue'] = $this->price($vatValue * $totalPrice / (1 + $vatValue)) . 'zł';
		}

		$data['summary'] = [
			'total' => $this->price($totalPrice),
			'net'   => $this->price($totalPrice / 1.23),
			'vat'   => $this->price($totalPrice - $totalPrice / 1.23),
		];

		$data['notes'][] = sprintf('Zamówienie nr %d', $order->id);
		if ($vatValue === self::VAT_ZERO) {
			$data['notes'][] = 'Zwolnienie z VAT na podstawie art. 113 ust. 1 Ustawy z dnia 11 marca 2004r. o podatku od towarów i usług';
		}

		$data['vatSummary'] = [];
		$data['vatSummaryTotal'] = ['net' => 0, 'vat' => 0, 'gross' => 0];
		$data['vatSummary'] = [
			'zw' => ['net' => 0, 'vat' => 0, 'gross' => 0],
			'23' => ['net' => 0, 'vat' => 0, 'gross' => 0],
		];
		foreach ($previousAdvances as $advance) {
			$net = $advance->vat === 'zw' ? $advance->amount : $advance->amount / 1.23;
			$vat = $advance->vat === 'zw' ? 0 : $advance->amount - $advance->amount / 1.23;
			$gross = $advance->amount;

			$data['vatSummary'][$advance->vat]['net'] += $net;
			$data['vatSummary'][$advance->vat]['vat'] += $vat;
			$data['vatSummary'][$advance->vat]['gross'] += $gross;

			$data['vatSummaryTotal']['net'] += $net;
			$data['vatSummaryTotal']['vat'] += $vat;
			$data['vatSummaryTotal']['gross'] += $gross;
		}
		$data['n'] = function ($price) {
			return number_format($price, 2, ',', ' ');
		};
		$data['invoiceOrder'] = $order;

		$this->renderAndSave('payment.invoices.final', $data, $invoice);

		return $invoice;
	}

	public function corrective(Order $order, InvoiceModel $corrected, $reason, $difference, bool $refund = true)
	{
		$previousAdvances = $order->invoices()->where('series', self::ADVANCE_SERIES_NAME)->get();
		$previousCorrectives = $order->invoices()->where('series', self::CORRECTIVE_SERIES_NAME)->get();
		$recentSettlement = $order->paid_amount - $previousAdvances->sum('amount');
		$vatValue = $corrected->vat === '23' ? 0.23 : 0;
		$vatString = $this->getVatString($vatValue);
		$totalPaid = $recentSettlement + $previousAdvances->sum('amount');
		$invoice = $order->invoices()->create([
			'number' => $this->nextNumberInSeries(self::CORRECTIVE_SERIES_NAME),
			'series' => self::CORRECTIVE_SERIES_NAME,
			'amount' => $difference,
			'vat'    => $corrected->vat,
			'corrected_invoice_id' => $corrected->id,
		]);

		$data = [
			'notes'            => [],
			'invoiceData'      => [
				'id'             => $invoice->id,
				'full_number'    => $invoice->full_number,
				'date'           => $invoice->created_at->format('d.m.Y'),
				'payment_date'   => $invoice->created_at->format('d.m.Y'),
				'payment_method' => 'przelew',
			],
			'difference'       => $difference,
			'correctedInvoice' => [
				'number' => $corrected->full_number,
			],
			'reason'           => $reason,
			'paid'             => $order->paid_amount,
			'refund'           => $refund,
		];

		$data['buyer'] = $this->getBuyerData($order->user);

		$data['ordersList'] = [
			[
				'product_name' => $order->product->invoice_name,
				'unit'         => 'szt.',
				'amount'       => 1,
			],
		];
		$data['ordersCorrected'] = [
			[
				'product_name' => $order->product->invoice_name,
				'unit'         => 'szt.',
				'amount'       => 1,
			],
		];
		$totalPrice = $order->total_with_coupon;
		$totalCorrected = $totalPrice + $difference;

		if ($coupon = $order->coupon) {
			$data['coupon'] = [
				'value'             => $order->coupon_amount,
				'total_with_coupon' => $order->total_with_coupon,
			];
			$data['notes'][] = "Cena obniżona na podstawie kuponu {$coupon->name}.";
		}

		// Calculate netto, brutto, VAT
		$data['ordersList'][0]['priceGross'] = $this->price($totalPrice);
		$data['ordersList'][0]['priceNet'] = $this->price($totalPrice / (1 + $vatValue));
		$data['ordersList'][0]['vat'] = $vatString;

		$data['ordersCorrected'][0]['priceGross'] = $this->price($totalCorrected);
		$data['ordersCorrected'][0]['priceNet'] = $this->price($totalCorrected / (1 + $vatValue));
		$data['ordersCorrected'][0]['vat'] = $vatString;

		$data['remainingAmountBefore'] = $this->price($order->total_with_coupon - $order->paid_amount);
		$data['remainingAmount'] = $this->price($order->total_with_coupon - $order->paid_amount - $difference);

		$data['previousAdvances'] = $previousAdvances;
		$data['recentSettlement'] = $recentSettlement;

		if ($vatValue === self::VAT_ZERO) {
			$data['ordersList'][0]['vatValue'] = '-';
			$data['ordersCorrected'][0]['vatValue'] = '-';
		} else {
			$data['ordersList'][0]['vatValue'] = $this->price($vatValue * $totalPrice / (1 + $vatValue)) . 'zł';
			$data['ordersCorrected'][0]['vatValue'] = $this->price($vatValue * $totalCorrected / (1 + $vatValue)) . 'zł';
		}

		$data['taxDifference'] = [
			'gross' => $difference,
			'vat'   => $this->price($vatValue * $difference / (1 + $vatValue)),
			'net'   => $this->price($difference / (1 + $vatValue)),
		];

		$data['summaryBefore'] = [
			'gross' => $order->paid_amount,
			'vat'   => $this->price($vatValue * $order->paid_amount / (1 + $vatValue)),
			'net'   => $this->price($order->paid_amount / (1 + $vatValue)),
		];

		$paidAfterCorrection = $order->paid_amount + $difference;
		$data['summaryAfter'] = [
			'gross' => $paidAfterCorrection,
			'vat'   => $this->price($vatValue * $paidAfterCorrection / (1 + $vatValue)),
			'net'   => $this->price($paidAfterCorrection / (1 + $vatValue)),
		];

		$data['notes'][] = sprintf('Zamówienie nr %d', $order->id);
		if ($vatValue === self::VAT_ZERO) {
			$data['notes'][] = 'Zwolnienie z VAT na podstawie art. 113 ust. 1 Ustawy z dnia 11 marca 2004r. o podatku od towarów i usług';
		}

		$this->renderAndSave('payment.invoices.corrective', $data, $invoice);

		return $invoice;
	}

	protected function getBuyerData(User $user)
	{
		if ($user->invoice) {
			return [
				'name'    => $user->invoice_name,
				'address' => $user->invoice_address,
				'zip'     => $user->invoice_zip,
				'city'    => $user->invoice_city,
				'country' => $user->invoice_country,
				'nip'     => 'NIP: ' . $user->invoice_nip,
			];
		} else {
			return [
				'name'    => $user->full_name,
				'address' => $user->address,
				'zip'     => $user->zip,
				'city'    => $user->city,
				'country' => '',
				'nip'     => '',
			];
		}
	}

	protected function renderAndSave($viewName, $data, $invoice)
	{
		$invoice->update(['meta' => $data]);
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

	private function getVatValue($currentSettlement = 0)
	{
		$sumAfterOrder = $this->advanceInvoiceSum() + $currentSettlement;

		if ($sumAfterOrder < self::VAT_THRESHOLD) {
			return self::VAT_ZERO;
		}

		return self::VAT_NORMAL;
	}

	private function getVatString($value)
	{
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

		return $orders->sum('paid_amount');
	}

	private function price($number)
	{
		return number_format($number, 2, ',', ' ');
	}
}
