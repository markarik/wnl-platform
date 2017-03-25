<?php


namespace Lib\Invoice;


use App\Models\Order;
use Facades\Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Invoice
{
	const PROFORMA_SERIES_NAME = 'PROFORMA';

	const ADVANCE_SERIES_NAME = 'F-ZAL';

	const FINAL_SERIES_NAME = 'FK';

	const VAT_THRESHOLD = 160000.00;

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
			'full_number' => $invoice->full_number,
			'invoice_id'  => $invoice->id,
			'name'        => $order->user->full_name,
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

	protected function renderAndSave($viewName, $data)
	{
		$view = view($viewName, ['data' => $data]);

		$html = $view->render();

		$pdf = PDF::loadHtml($html);

		Storage::put("invoices/{$data['invoice_id']}.pdf", $pdf->stream());
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

	private function advanceInvoiceSum()
	{
		$orders = Order::whereHas('invoices', function ($query) {
			$query->where('series', self::ADVANCE_SERIES_NAME);
		})->get();

		return $orders->sum('total_with_coupon');
	}

}
