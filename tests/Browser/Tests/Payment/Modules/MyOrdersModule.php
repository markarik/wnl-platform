<?php


namespace Tests\Browser\Tests\Payment\Modules;

use App;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Assert;
use Tests\BethinkBrowser;
use Tests\Browser\Pages\Course\Components\Navigation;

class MyOrdersModule
{
	public function end(BethinkBrowser $browser)
	{
		$this->assertOrderPlaced($browser);
		$this->assertPaid($browser);
		if (!empty($browser->studyBuddy)) {
			$this->assertStudyBuddy($browser);
		}
	}

	public function studyBuddy(BethinkBrowser $browser)
	{
		if (!empty($browser->studyBuddy)) {
			$this->assertStudyBuddy($browser);

			return;
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
	}

	protected function assertOrderPlaced(BethinkBrowser $browser)
	{
		$order = $browser->order;

		$browser->waitForText('Twoje zamówienia', 60);
		$browser->waitForText('Zamówienie numer ' . $order->id);

		if (empty($browser->coupon)) {
			$browser->waitForText('Study Buddy');
		} else {
			$coupon = $browser->coupon;
			$browser->waitForText($coupon->name);
		}
	}

	protected function assertPaid(BethinkBrowser $browser)
	{
		$browser->order = $browser->order->fresh();
		if ($browser->order->method === 'instalments') {
			/** @var Collection $instalments */
			$instalments = $browser->order->instalments['instalments'];
			/** @var App\Models\OrderInstalment $firstInstalment */
			$firstInstalment = $instalments->get(0);
			$browser->order->paid_amount = $firstInstalment->left_amount;
			$browser->order->save();
		}

		if ($browser->order->method === 'transfer') {
			$browser->order->paid = 1;
			$browser->order->paid_amount = $browser->order->total_with_coupon;
			$browser->order->save();
		}

		$browser->refresh();
		$browser->waitForText('Wpłacono', 60);
	}

	protected function assertStudyBuddy(BethinkBrowser $browser)
	{
		$studyBuddy = $browser->studyBuddy->fresh();
		$originalOrder = $studyBuddy->order;

		if ($originalOrder->method === 'instalments') {
			Assert::assertEquals('refunded', $studyBuddy->status, 'Study buddy status is refunded');

			return;
		}

		Assert::assertEquals('awaiting-refund', $studyBuddy->status, 'Study buddy status is refunded');
	}
}
