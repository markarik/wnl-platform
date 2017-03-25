<?php


namespace Lib\Invoice;


use App\Models\Order;
use Facades\Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Storage;

class Invoice
{
	const PROFORMA_SERIES_NAME = 'PROFORMA';

	public function proforma(Order $order)
	{
		$data = [
			'name' => 'Kuba Karmiński',
		];

		$view = view('payment.invoices.pro-forma', ['data' => $data]);
//		$view->render();

		$html = $view->render();

//		$order->invoices()->create([
//			'number' => 1,
//			'series' => self::PROFORMA_SERIES_NAME,
//		]);

		$pdf = PDF::loadHtml($html);

		Storage::put("invoices/example.pdf", $pdf->stream());
	}

	public function advance()
	{

	}


}