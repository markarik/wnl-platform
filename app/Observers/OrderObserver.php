<?php


namespace App\Observers;


use App\Jobs\CreateSubscription;
use App\Jobs\OrderConfirmed;
use App\Jobs\OrderPaid;
use App\Jobs\OrderStudyBuddy;
use App\Jobs\PopulateUserCoursePlan;
use App\Models\Order;
use App\Notifications\OrderCreated;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;


class OrderObserver
{
	use DispatchesJobs, Notifiable;

	public function updated(Order $order)
	{
		\Log::notice("OrderObserver: Order #{$order->id} updated");
		$settlement = $order->paid_amount - $order->getOriginal('paid_amount');
		if (!$order->isDirty(['paid']) && $order->isDirty(['paid_amount']) && $settlement > 0) {
			\Log::notice(">>> OrderObserver: #{$order->id} paid amount is dirty");

			if ($order->paidAmountSufficient() && !$order->paid) {
				\Log::notice("___ OrderObserver: #{$order->id} marking order as paid");
				$order->paid = true;
				$order->paid_at = Carbon::now();
				$order->save();
				$this->dispatchNow(new CreateSubscription($order));
			}

			\Log::notice("OrderObserver: Dispatching OrderPaid for order #$order->id");
			$this->dispatch(new OrderPaid($order));

			\Log::notice("OrderPaid: handleStudyBuddy called for order #{$order->id}");
			dispatch_now(new OrderStudyBuddy($order));
		} else {
			\Log::notice(
				"OrderObserver: Order #$order->id NOT updated. Order was not dirty or settlement was smaller than 0"
			);
		}

		if (!$order->paid &&
			$order->getOriginal('method') === null &&
			$order->method !== null
		) {
			$this->handlePaymentMethodSet($order);
		}

		if ($order->getOriginal('coupon_id') &&
			$order->getOriginal('coupon_id') !== $order->coupon_id
		) {
			$this->handleCouponChange($order);
		}
	}

	public function routeNotificationForSlack()
	{
		if (App::environment('production')) {
			return env('SLACK_ORDERS_URL');
		} else {
			return env('SLACK_TEST');
		}
	}

	protected function handlePaymentMethodSet(Order $order)
	{
		\Log::debug('Order payment method set, decrementing product quantity.');
		$this->dispatch(new OrderConfirmed($order));
		$order->product->quantity--;
		$order->product->save();

		if ($order->coupon && $order->coupon->times_usable > 0) {
			$order->coupon->times_usable--;
			$order->coupon->save();
		}

		if (intval($order->total_with_coupon) === 0) {
			\Log::notice('Order total is 0, marking as paid and dispatching OrderPaid job.');
			$order->paid = true;
			$order->save();
			$this->dispatch(new OrderPaid($order));
			$this->dispatchNow(new CreateSubscription($order));
		}

		if ($order->method === 'instalments') {
			$order->generateAndSavePaymentSchedule();
		}

		$this->notify(new OrderCreated($order));
	}

	protected function handleCouponChange(Order $order)
	{
		\Log::debug('Order coupon changed.');
		if ($order->studyBuddy) {
			$order->studyBuddy->delete();
		}

		if ($order->method === 'instalments') {
			$order->generateAndSavePaymentSchedule();
		}
	}
}
