<?php


namespace Tests\Browser\Tests\Payment\Modules;

use App;
use PHPUnit\Framework\Assert;
use Tests\BethinkBrowser;
use Tests\Browser\Pages\Course\Components\Navigation;

class MyOrdersModule
{
	public function studyBuddyInitiator(BethinkBrowser $browser)
	{
		$order = $browser->order;
		$studyBuddy = $order->studyBuddy;
		Assert::assertTrue($studyBuddy instanceof App\Models\StudyBuddy);

		$browser->studyBuddy = $studyBuddy;
		$browser->waitForText($studyBuddy->code, 60);

		$browser
			->on(new Navigation)
			->logoutUser();
	}

	public function payNextInstalment(BethinkBrowser $browser)
	{
		$order = $browser->order;
		$browser->click('.order[data-order-id="' . $order->id . '"] [data-button="pay-next-instalment"]');
	}

	public function assertOrderPlaced(BethinkBrowser $browser)
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

	public function assertPaid(BethinkBrowser $browser, string $expectedAmount)
	{
		$browser->waitForText('Wpłacono ' . $expectedAmount);
	}

	public function assertNotPaid(BethinkBrowser $browser)
	{
		$order = $browser->order;
		$browser->assertSeeIn('.order[data-order-id="' . $order->id . '"] .current-payment', 'KWOTA DO ZAPŁATY: 1500ZŁ');
	}

	public function assertInstalment(BethinkBrowser $browser, int $instalmentNumber, string $expectedText)
	{
		$order = $browser->order;
		$browser->assertSeeIn('.order[data-order-id="' . $order->id . '"] .instalment-amount[data-instalment="' . $instalmentNumber . '"]', $expectedText);
	}

	public function assertStudyBuddyAwaitingRefund(BethinkBrowser $browser)
	{
		$studyBuddy = $browser->studyBuddy->fresh();
		Assert::assertEquals('awaiting-refund', $studyBuddy->status, 'Study buddy status is awaiting refund');
	}

	public function assertStudyBuddyRefunded(BethinkBrowser $browser)
	{
		$studyBuddy = $browser->studyBuddy->fresh();
		Assert::assertEquals('refunded', $studyBuddy->status, 'Study buddy status is refunded');
	}
}
