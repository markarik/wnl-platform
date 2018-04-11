<?php

namespace App\Console\Commands;

use App\Mail\TransferReminder;
use App\Models\Order;
use App\Models\PaymentReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class OrdersHandleUnpaid extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'orders:handleUnpaid';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Look for past due payments and send reminders.';

	/**
	 * Create a new command instance.
	 *
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
		$beforeDue = Carbon::today()->subDays(6);
		$pastDue = Carbon::today()->subDays(9);
		$expired = Carbon::today()->subDays(30);

		$this->handleTransfer($pastDue, $beforeDue);
		$this->handleInstalments($pastDue, $beforeDue);

		PaymentReminder::where('created_at', '<', $expired)->delete();

		return;
	}

	protected function handleInstalments($pastDue, $beforeDue)
	{
		$remind = Order::whereHas('orderInstalments',
			function ($query) use ($pastDue, $beforeDue) {
				$query
					->where('paid', 0)
					->whereBetween('due_date', [$pastDue, $beforeDue]);
			})
			->where('method', 'instalments')
			->where('canceled', '!=', 1)
			->limit(1)
			->get();
	}

	protected function handleTransfer($pastDue, $beforeDue)
	{
		$remind = Order::with('paymentReminders')
			->where('paid', 0)
			->whereBetween('created_at', [$pastDue, $beforeDue])
			->where('method', 'transfer')
			->where('canceled', '!=', 1)
			->limit(1)
			->get();

		foreach ($remind as $order) {
			if ($order->paymentReminders->count() === 0) {
				Mail::to($order->user)->send(new TransferReminder($order));
				$order->paymentReminders()->create();
			}
		}

		$cancel = Order::with('paymentReminders')
			->where('paid', 0)
			->where('created_at', '<', $pastDue)
			->where('method', 'transfer')
			->where('canceled', '!=', 1)
			->get();

		$now = Carbon::now();
		foreach ($cancel as $order) {
			$reminder = $order->paymentReminders->last();

			if ($now->diffInDays($reminder->created_at) >= 2) {
				$order->cancel();
			}
		}
	}
}
