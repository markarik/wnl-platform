<?php

namespace App\Console\Commands;

use App\Mail\AccountSuspendedUnpaidInstalment;
use App\Mail\InstalmentReminder;
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

//		$this->handleTransfer($pastDue, $beforeDue);
		$this->handleInstalments($pastDue, $beforeDue);

		PaymentReminder::where('created_at', '<', $expired)->delete();

		return;
	}

	protected function handleInstalments($pastDue, $beforeDue)
	{
		\DB::listen(function ($query) {
			$format = str_replace('?', '%s', $query->sql);
			$queryFormatted = vsprintf($format, $query->bindings);
			dump($queryFormatted, $query->time);
		});
		$remind = Order::whereHas('orderInstalments',
			function ($query) use ($pastDue, $beforeDue) {
				$query
					->whereRaw('order_instalments.paid_amount < order_instalments.amount');
//					->whereBetween('due_date', [$pastDue, $beforeDue]);
			})
			->where('method', 'instalments')
			->where('canceled', '!=', 1)
			->limit(1)
			->get();

		foreach ($remind as $order) {
			$instalment = $order->orderInstalments->where('left_amount', '>', 0)->first();
			$reminders = $order->paymentReminders->where('instalment_number', $instalment->order_number);

			if ($reminders->count() === 0) {
				Mail::to($order->user)->send(new InstalmentReminder($order, $instalment));
				$order->paymentReminders()->create([
					'instalment_number' => $instalment->order_number,
				]);
			}
		}

		$suspend = Order::whereHas('orderInstalments',
			function ($query) use ($pastDue, $beforeDue) {
				$query
					->whereRaw('order_instalments.paid_amount < order_instalments.amount');
//					->where('due_date', '<',$pastDue);
			})
			->where('method', 'instalments')
			->where('canceled', '!=', 1)
			->limit(1)
			->get();

		$now = Carbon::now();
		foreach ($suspend as $order) {
			$reminder = $order->paymentReminders->last();
			$instalment = $order->orderInstalments
				->where('left_amount', '>', 0)
				->first();

			if ($now->diffInDays($reminder->created_at) >= 2 &&
				$reminder->instalment_number === $instalment->order_number
			) {
				$order->user->suspend();

				Mail::to($order->user)
					->send(new AccountSuspendedUnpaidInstalment($order, $instalment));
			}
		}

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
			->whereHas('paymentReminders')
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
