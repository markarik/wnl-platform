<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class OrderInvoices extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'order:invoices {order}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

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
		$orderId = $this->argument('order');
		$order = Order::find($orderId);
		$headers = ['id', 'full_number', 'amount', 'vat', 'created_at'];
		$rows = $order->invoices->map(function ($invoice) {
			return [
				$invoice->id,
				$invoice->full_number,
				$invoice->amount,
				$invoice->vat,
				$invoice->created_at,
			];
		})->toArray();
		$this->table($headers, $rows);
	}
}
