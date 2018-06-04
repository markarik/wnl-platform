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
	const VAT_SERIES_NAME = 'FV';
	const DELIMITER = ';';

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'invoices:csv';

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
				$query->orWhere('series', self::VAT_SERIES_NAME);
			})->get();

		$schema = $this->getSchema();

		$data = '';
		$data .= $this->printHeaders($schema);

		foreach ($orders as $order) {
			if (!$order->user) {
				continue;
			}

			$finalNet = 0;
			$finalVat = 0;
			$finalGross = 0;

			foreach ($order->invoices as $invoice) {
				$valuesOverrides = [];

				if ($invoice->series === 'PROFORMA') {
					continue;
				}

				if ($invoice->series === 'F-ZAL') {
					$finalNet = $finalNet + $this->price($invoice->netValue);
					$finalVat = $finalVat + $this->price($invoice->vatAmount);
					$finalGross = $finalGross + $this->price($invoice->amount);
				}

				if ($invoice->series === 'FK') {
					$valuesOverrides['netValue'] = $finalNet;
					$valuesOverrides['amount'] = $finalGross;
					$valuesOverrides['vatAmount'] = $finalVat;
				}

				$data .= $this->printLine($invoice, $order, $schema, $valuesOverrides);
			}
		}

		\Storage::put('exports/invoices ' . Carbon::now()->toDateTimeString() . '.csv', $data);
	}

	public function printLine($invoice, $order, $schema, $valuesOverrides)
	{
		$isCompany = $order->user->invoice;

		if ($invoice->series === 'FK') {
			$netValue = $this->price($valuesOverrides['netValue']);
			$vatAmount = $this->price($valuesOverrides['vatAmount']);
			$grossValue = $this->price($valuesOverrides['amount']);
		} else {
			$netValue = $this->price($invoice->netValue);
			$vatAmount = $this->price($invoice->vatAmount);
			$grossValue = $this->price($invoice->amount);
		}

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
		$schema['Wartość netto'] = $netValue;
		$schema['Stawka VAT'] = $invoice->vat;
		$schema['Kwota VAT'] = $vatAmount;
		$schema['Wartość brutto'] = $grossValue;

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
