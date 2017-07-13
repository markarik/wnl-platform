<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Console\Command;

class InvoicesExport extends Command
{
	const ADVANCE_SERIES_NAME = 'F-ZAL';
	const FINAL_SERIES_NAME = 'FK';
	const DELIMITER = ';';

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'invoice:csv';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Export all invoices as csv files';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$orders = Order::with(['invoices', 'user', 'product'])
			->whereHas('invoices', function ($query) {
				$query->where('series', self::ADVANCE_SERIES_NAME);
				$query->orWhere('series', self::FINAL_SERIES_NAME);
			})->get();

		$schema = $this->getSchema();

		$data = '';
		$data .= $this->printHeaders($schema);

		foreach ($orders as $order) {
			if (!$order->user) {
				continue;
			}

			foreach ($order->invoices as $invoice) {
				if ($invoice->series === 'PROFORMA') {
					continue;
				}

				$data .= $this->printLine($invoice, $order, $schema);
			}
		}

		\Storage::put('exports/invoices ' . Carbon::now()->toDateTimeString() . '.csv', $data);
	}

	public function printLine($invoice, $order, $schema)
	{
		$isCompany = $order->user->invoice;

		$schema['Seria'] = $invoice->series;
		$schema['Numer'] = $invoice->number;
		$schema['Data wystawienia'] = $invoice->created_at->toDateTimeString();
		$schema['ID kontrahenta'] = $order->user->id;
		$schema['Firma?'] = $isCompany ? 'TAK' : 'NIE';
		$schema['Imię/Nazwa'] = $isCompany ? $order->user->invoice_name : $order->user->first_name;
		$schema['Nazwisko'] = $isCompany ? '' : $order->user->last_name;
		$schema['Adres'] = $isCompany ? $order->user->invoice_address : $order->user->address;
		$schema['NIP'] = $isCompany ? $order->user->invoice_nip : '';
		$schema['Sposób płatności'] = $order->method;
		$schema['Produkt'] = $order->product->name;
		$schema['Numer produktu'] = $order->product->id;
		$schema['Wartość netto'] = $this->price($invoice->netValue);
		$schema['Stawka VAT'] = $invoice->vat;
		$schema['Kwota VAT'] = $this->price($invoice->vatAmount);
		$schema['Wartość brutto'] = $this->price($invoice->amount);

		$fields = array_map(function ($element) {
			return '"' . $element . '"';
		}, $schema);

		return implode(self::DELIMITER, $fields) . PHP_EOL;
	}

	protected function price($number)
	{
		return number_format($number, 2, '.', '');
	}

	public function printHeaders($schema)
	{
		$headers = array_map(function ($element) {
			return '"' . $element . '"';
		}, array_keys($schema));

		return implode(self::DELIMITER, $headers) . PHP_EOL;
	}

	protected function getSchema()
	{
		return [
			'Seria' => '#',
			'Numer' => '#',
			'Data wystawienia' => '#',
			'ID kontrahenta' => '#',
			'Firma?' => '#',
			'Imię/Nazwa' => '#',
			'Nazwisko' => '#',
			'Adres' => '#',
			'NIP' => '#',
			'Sposób płatności' => '#',
			'Produkt' => '#',
			'Numer produktu' => '#',
			'Wartość netto' => '#',
			'Stawka VAT' => '#',
			'Kwota VAT' => '#',
			'Wartość brutto' => '#',
		];
	}
}
