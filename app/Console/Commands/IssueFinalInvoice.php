<?php

namespace App\Console\Commands;

use App\Jobs\IssueFinalInvoice as IssueFinalAndSend;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Lib\Invoice\Invoice;

class IssueFinalInvoice extends Command
{
	use DispatchesJobs;

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'invoice:final {id? : The ID of the order}';

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
		$orderId = $this->argument('id');

		if ($orderId) {
			$order = Order::find($orderId);

			if (!$order) {
				$this->error("Order {$orderId} not found.");

				return;
			}

			$this->dispatch(new IssueFinalAndSend($order));
		} else {
			/** @var Order[] $orders */
			$orders = Order::with(['product'])
				->whereDoesntHave('invoices', function ($query) {
					$query
						->where('series', Invoice::FINAL_SERIES_NAME)
						->orWhere('series', Invoice::VAT_SERIES_NAME);
				})
				->where('paid', 1)
				->get();

			foreach ($orders as $order) {
				if (!$order->canceled){
					$this->dispatch(new IssueFinalAndSend($order));
				}
			}
		}

		return;
	}
}
