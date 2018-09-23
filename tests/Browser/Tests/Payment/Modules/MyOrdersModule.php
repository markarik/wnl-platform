<?php


namespace Tests\Browser\Tests\Payment\Modules;

use App, Mail;
use PHPUnit\Framework\Assert;
use Tests\Browser\Pages\Course\Components\Navigation;

class MyOrdersModule
{
	public function end($browser)
	{
		$this->assertOrderPlaced($browser);
		$this->assertPaid($browser);
		if (!empty($browser->studyBuddy)) {
			$this->assertStudyBuddy($browser);
		}

		return false;
	}

	public function studyBuddy($browser)
	{
		if (!empty($browser->studyBuddy)) {
			$this->assertStudyBuddy($browser);

			return false;
		}

		$this->assertOrderPlaced($browser);
		$this->assertPaid($browser);

		$order = $browser->order;
		$studyBuddy = $order->studyBuddy;
		Assert::assertTrue($studyBuddy instanceof App\Models\StudyBuddy);

		$browser->studyBuddy = $studyBuddy;
		$browser->waitForText($studyBuddy->code, 60);

		$browser
			->on(new Navigation)
			->logoutUser();

		return [VoucherModule::class];
	}

	protected function assertOrderPlaced($browser)
	{
		$order = $browser->order;

		$browser->waitForText('Twoje zamówienia', 60);
		$browser->pause(1000);
		$browser->waitForText('Zamówienie numer ' . $order->id);

		if (empty($browser->coupon)) {
			$browser->waitForText('Study Buddy');
		} else {
			$coupon = $browser->coupon;
			$browser->waitForText($coupon->name);
		}

	}

	protected function assertPaid($browser)
	{
		$browser->order = $browser->order->fresh();
		if ($browser->order->method === 'instalments') {
			$browser->order->paid = 1;
			$browser->order->paid_amount = $browser->order->instalments['instalments'][0]['left'];
			$browser->order->save();
		}

		if ($browser->order->method === 'transfer') {
			$browser->order->paid = 1;
			$browser->order->paid_amount = $browser->order->total_with_coupon;
			$browser->order->save();
		}

		$order = $browser->order;

		if (!App::environment(['staging', 'sandbox']) && $order->method === 'online') {
			return;
		}

		$browser->refresh();
		$browser->waitForText('Wpłacono', 60);
	}

	protected function assertStudyBuddy($browser)
	{
		$studyBuddy = $browser->studyBuddy->fresh();
		$originalOrder = $studyBuddy->order;

		if ($originalOrder->method === 'instalments') {
			Assert::assertTrue($studyBuddy->status === 'refunded');

			return;
		}

		Assert::assertTrue($studyBuddy->status === 'awaiting-refund');
	}
}
