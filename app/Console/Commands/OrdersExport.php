<?php

namespace App\Console\Commands;

use Storage;
use App\Models\User;
use App\Models\Order;
use App\Models\Invoice;
use Illuminate\Console\Command;

class OrdersExport extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'orders:export';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Exports paid orders with users and respective invoices';

	private $methodsMap = [
		'instalments' => 'Raty',
		'online'      => 'Przelewy24',
		'transfer'    => 'Zwykły przelew',
	];

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
		$orders = Order::with(['user', 'invoices'])->where('paid', 1)->get();

		$schema = $this->getSchema();

		$data = '';
		$data .= $this->printHeaders($schema);

		foreach ($orders as $order) {
			$data .= $this->printLine($order, $schema);
		}

		Storage::put('exports/orders-' . date(DATE_ATOM) . '.csv', $data);

		return;
	}

	public function printLine($order, $schema)
	{
		$user = $order->user()->first();
		$invoices = $order->invoices()->get();
		$invoicesList = '';

		foreach($invoices as $invoice) {
			if ($invoice->series !== config('invoice.proforma_series')) {
				$invoicesList .= $invoice->series . '/' . $invoice->number . ' ';
			}
		}

		$schema['Numer zamówienia'] = $order->id;
		$schema['Imię'] = $user->first_name;
		$schema['Nazwisko'] = $user->last_name;
		$schema['Wystawione faktury'] = $invoicesList;
		$schema['Metoda płatności'] = $this->methodsMap[$order->method];
		$schema['Nr transakcji w Przelewy 24'] = $order->external_id;

		$fields = array_map(function ($element) {
			return '"' . $element . '"';
		}, $schema);

		return implode(',', $fields) . PHP_EOL;
	}

	public function printHeaders($schema)
	{
		$headers = array_map(function ($element) {
			return '"' . $element . '"';
		}, array_keys($schema));

		return implode(',', $headers) . PHP_EOL;
	}

	protected function getSchema()
	{
		return [
			'Numer zamówienia'            => '#',
			'Imię'                        => '#',
			'Nazwisko'                    => '#',
			'Wystawione faktury'          => '#',
			'Metoda płatności'            => '#',
			'Nr transakcji w Przelewy 24' => '#',
		];
	}
}
