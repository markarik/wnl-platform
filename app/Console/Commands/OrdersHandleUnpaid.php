<?php

namespace App\Console\Commands;

use App\Mail\AccountSuspendedUnpaidInstalment;
use App\Mail\InstalmentReminder;
use App\Mail\TransferReminder;
use App\Models\Order;
use App\Models\PaymentReminder;
use App\Models\SiteWideMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class OrdersHandleUnpaid extends CommandWithMonitoring
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'orders:handleUnpaid {--time-shift=} {--mail-debug}';

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

	protected $mailDebug;

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handleCommand()
	{
		$this->mailDebug = $this->option('mail-debug');
		$timeShift = $this->option('time-shift');

		if ($timeShift) {
			Carbon::setTestNow(Carbon::now()->addDays((int) $timeShift));
		}

		$this->handleUnpaidOrders();
		$this->handleUnpaidInstalment();

		return;
	}

	protected function handleUnpaidOrders()
	{
		$now = Carbon::now();
		$sixDaysAgo = Carbon::today()->subDays(6);

		$orders = Order::with('paymentReminders')
			->where('paid', 0)
			->where('created_at', '<=', $sixDaysAgo)
			->whereIn('method', ['transfer', 'instalments', 'online'])
			->where('canceled', '!=', 1)
			->get();

		if ($orders->count() === 0) return;

		foreach ($orders as $order) {
			if ($this->isProductAlreadyBought($order)) {
				$order->cancel();
				continue;
			}

			SiteWideMessage::firstOrCreate([
				'user_id' => $order->user_id,
				'slug' => "order-payment-reminder-{$order->id}",
				'start_date' => Carbon::today(),
				'end_date' => Carbon::tomorrow(),
				'target' => SiteWideMessage::SITE_WIDE_ALERT_DISPLAY_TARGET,
				'message' => trans('site_wide_messages.unpaid-order-reminder', ['orderId' => $order->id])
			]);

			if ($order->paymentReminders->count() === 0) {
				$this->mail($order, TransferReminder::class);
				$order->paymentReminders()->create();
			} else {
				$reminder = $order->paymentReminders->last();

				if ($now->diffInWeekdays($reminder->created_at) >= 4) {
					$order->cancel();
				}
			}
		}
	}

	protected function handleUnpaidInstalment()
	{
		$beforeDue = Carbon::today()->addDays(7);
		$orders = Order::whereHas('orderInstalments',
			function ($query) use ($beforeDue) {
				$query
					->whereRaw('order_instalments.paid_amount < order_instalments.amount')
					->whereDate('due_date', "<=", $beforeDue)
					// Account is suspended after 4 days from the last sent reminder. For safety use longer period and double-check
					->whereDate('due_date', ">=", Carbon::today()->subDays(7));
			})
			->where('method', 'instalments')
			->where('canceled', '!=', 1)
			->where('paid', 1)
			->get();

		foreach ($orders as $order) {
			$instalment = $this->getFirstUnpaidInstalment($order);

			SiteWideMessage::firstOrCreate([
				'user_id' => $order->user_id,
				'slug' => "instalment-reminder-{$instalment->id}",
				'start_date' => Carbon::today(),
				'end_date' => Carbon::tomorrow(),
				'target' => SiteWideMessage::SITE_WIDE_ALERT_DISPLAY_TARGET,
				'message' => trans('site_wide_messages.unpaid-instalment-reminder', [
					'dueDate' => Carbon::parse($instalment->due_date)->format('Y-m-d'),
					'orderId' => $order->id
				])
			]);

			// next instalment due date is in one day
			if ($instalment->due_date <= Carbon::today()->addDays(1)) {
				$reminders = $order->paymentReminders
					->where('instalment_number', $instalment->order_number);

				if ($reminders->count() === 0) {
					$this->mail($order, InstalmentReminder::class, $instalment);
					$order->paymentReminders()->create([
						'instalment_number' => $instalment->order_number,
					]);
				} else {
					if ($this->shouldSuspend($order, $instalment)) {
						$order->user->suspend();
						$this->mail($order, AccountSuspendedUnpaidInstalment::class, $instalment);
					}
				}

			}
		}
	}

	protected function mail($order, $mail, $instalment = null)
	{
		if ($instalment) {
			if ($this->mailDebug) {
				$this->info("{$mail} -> order {$order->id} from {$order->created_at}, instalment num. {$instalment->order_number}");
			} else {
				Mail::to($order->user)->send(new $mail($order, $instalment));
			}
			return;
		}

		if ($this->mailDebug) {
			$this->info("{$mail} -> order {$order->id} from {$order->created_at}");
		} else {
			Mail::to($order->user)->send(new $mail($order));
		}

		return;
	}

	protected function shouldSuspend($order, $instalment)
	{
		$now = Carbon::now();
		$reminder = $order->paymentReminders->last();

		return
			!$order->user->suspended &&
			$now->diffInWeekdays($reminder->created_at) >= 4 &&
			$reminder->instalment_number === $instalment->order_number;
	}

	protected function getFirstUnpaidInstalment($order) {
		return $order->orderInstalments
			->where('left_amount', '>', 0)
			->sortBy('order_number')
			->first();
	}

	protected function isProductAlreadyBought(Order $order): bool {
		return !empty($order->user->getProducts()->first(function($product) use ($order) {
			return $product->id === $order->product->id;
		}));
	}
}
