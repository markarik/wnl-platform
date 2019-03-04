<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Support\Collection;

class StudyBuddyCancel extends CommandWithMonitoring
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sb:cancel {orderId?}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Cancel expired Studdy Buddy coupons';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handleCommand()
	{
		$ids = $this->argument('orderId');
		if ($ids) {
			$orders = Order::whereIn('id', explode(',', $ids));
		} else {
			$orders = Order::whereHas('product', function ($query) {
				$query->where('course_end', '<', now());
			});
		}

		/** @var Order[]|Collection $orders */
		$orders = $orders->whereHas('studyBuddy', function($query){
			$query->where('status', '<>', 'expired');
		})->get();

		foreach ($orders as $order) {
			$order->studyBuddy->status = 'expired';
			$order->studyBuddy->save();
			$order->studyBuddy->coupon->times_usable = 0;
			$order->studyBuddy->coupon->save();
		}

		$this->info('OK, ' . $orders->count() . ' study buddies canceled.');

		return;
	}
}
