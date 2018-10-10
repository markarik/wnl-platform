<?php

namespace App\Console\Commands;

use App\Mail\AccountSuspendedUnpaidInstalment;
use App\Mail\InstalmentReminder;
use App\Mail\TransferReminder;
use App\Models\Order;
use App\Models\PaymentReminder;
use App\Models\SiteWideMessage;
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
		$this->mailDebug = $this->option('mail-debug');
		$timeShift = $this->option('time-shift');

		if ($timeShift) {
			Carbon::setTestNow(Carbon::now()->addDays($timeShift));
		}

		$expired = Carbon::today()->subDays(30);

		$this->handleUnpaidOrders();
		$this->handleUnpaidInstalment();

		PaymentReminder::where('created_at', '<', $expired)->delete();

		return;
	}

	protected function handleUnpaidOrders()
	{
		$now = Carbon::now();
		$sixDaysAgo = Carbon::today()->subDays(6);

		$orders = Order::with('paymentReminders')
			->where('paid', 0)
			->where('created_at', '<=', $sixDaysAgo)
			->whereIn('method', ['transfer', 'instalments'])
			->where('canceled', '!=', 1)
			->get();

		if ($orders->count() === 0) return;

		foreach ($orders as $order) {
			if ($order->paymentReminders->count() === 0) {
				$this->mail($order, TransferReminder::class);
				$order->paymentReminders()->create();
			} else {
				$reminder = $order->paymentReminders->last();

				if ($now->diffInWeekdays($reminder->created_at) >= 2) {
					if ($this->mailDebug) $this->mail($order, 'canceled');
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
					->whereDate('due_date', "<=", $beforeDue);
			})
			->where('method', 'instalments')
			->where('canceled', '!=', 1)
			->where('paid', 1)
			->get();

		foreach ($orders as $order) {
			$instalment = $this->getFirstUnpaidInstalment($order);

			$reminders = $order->paymentReminders
				->where('instalment_number', $instalment->order_number);

			SiteWideMessage::firstOrCreate([
				'user_id' => $order->user_id,
				'slug' => "instalment-reminder-{$instalment->id}",
				'start_date' => Carbon::today(),
				'end_date' => Carbon::tomorrow(),
				'target' => SiteWideMessage::SITE_WIDE_ALERT_DISPLAY_TARGET,
				'message' => trans('site_wide_messages.unpaid-instalment-reminder', ['orderId' => $order->id])
			]);

			// send email when reminder not send and instalment due date is in one day
			if ($instalment->due_date <= Carbon::today()->addDays(1) && $reminders->count() === 0) {
				$this->mail($order, InstalmentReminder::class, $instalment);
				$order->paymentReminders()->create([
					'instalment_number' => $instalment->order_number,
				]);
				continue;
			}

			if ($this->shouldSuspend($order, $instalment)) {
				$order->user->suspend();
				$this->mail($order, AccountSuspendedUnpaidInstalment::class, $instalment);
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
			$now->diffInWeekdays($reminder->created_at) >= 2 &&
			$reminder->instalment_number === $instalment->order_number;
	}

	protected function getFirstUnpaidInstalment($order) {
		return $order->orderInstalments
			->where('left_amount', '>', 0)
			->sortBy('order_number')
			->first();
	}
}
