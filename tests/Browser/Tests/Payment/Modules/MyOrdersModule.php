<?php


namespace Tests\Browser\Tests\Payment\Modules;

use App;
use App\Models\Coupon;
use PHPUnit\Framework\Assert;
use Tests\BethinkBrowser;
use Tests\Browser\Pages\Payment\AccountPage;
use Tests\Browser\Pages\Payment\MyOrdersPage;
use Tests\Browser\Pages\Payment\VoucherPage;

class MyOrdersModule
{
	public function studyBuddyInitiator(BethinkBrowser $browser): App\Models\StudyBuddy
	{
		$order = $browser->order;
		$studyBuddy = $order->studyBuddy;
		Assert::assertTrue($studyBuddy instanceof App\Models\StudyBuddy);

		$browser->studyBuddy = $studyBuddy;
		$browser->waitForText($studyBuddy->code, 60);

		return $studyBuddy;
	}

	public function assertStuddyBuddyNotActive(BethinkBrowser $browser)
	{
		$studyBuddy = $browser->order->studyBuddy;
		if ($studyBuddy) {
			$studyBuddy = $studyBuddy->fresh();
		}

		Assert::assertNotEquals('active', $studyBuddy ? $studyBuddy->status : null);
	}

	public function payNow(BethinkBrowser $browser)
	{
		$order = $browser->order;
		$browser->click('.order[data-order-id="' . $order->id . '"] [data-button="pay-now"]');
	}

	public function payNextInstalment(BethinkBrowser $browser)
	{
		$order = $browser->order;
		$browser->click('.order[data-order-id="' . $order->id . '"] [data-button="pay-next-instalment"]');
	}

	public function useCoupon(BethinkBrowser $browser, $value) {
		$coupon = factory(Coupon::class)->create([
			'value' => $value
		]);

		$browser->coupon = $coupon;

		$browser
			->visit(new MyOrdersPage())
			->click('@discounts-tab')
			->click('@add-discount')
			->type('code', $coupon->code)
			->click('@use')
			->waitForText('Naliczona zniżka');
	}

	public function assertOrderPlaced(BethinkBrowser $browser)
	{
		$order = $browser->order;

		$browser
			->waitForText('Twoje zamówienia', 60)
			->waitForText('Zamówienie numer ' . $order->id);

		if (empty($browser->coupon)) {
			$browser->waitForText('Study Buddy');
		} else {
			$coupon = $browser->coupon;
			$browser->waitForText($coupon->name);
		}
	}

	public function assertPaid(BethinkBrowser $browser, string $expectedAmount)
	{
		$browser->waitForText('Wpłacono ' . $expectedAmount, 60);
	}

	public function assertNotPaid(BethinkBrowser $browser)
	{
		$order = $browser->order;
		$browser->assertSeeIn('.order[data-order-id="' . $order->id . '"] .current-payment', 'KWOTA DO ZAPŁATY: 1500ZŁ');
	}

	public function assertInstalment(BethinkBrowser $browser, int $instalmentNumber, string $expectedText)
	{
		$order = $browser->order;
		$browser
			->waitForText('Zamówienie numer ' . $order->id)
			->assertSeeIn('.order[data-order-id="' . $order->id . '"] .instalment-amount[data-instalment="' . $instalmentNumber . '"]', $expectedText);
	}

	public function assertStudyBuddyAwaitingRefund(BethinkBrowser $browser)
	{
		$order = $browser->order;

		$browser
			->refresh()
			->waitForText('Zamówienie numer ' . $order->id)
			->assertSeeIn('.order[data-order-id="' . $order->id . '"]', 'Twój Study Buddy dołączył już do kursu!');
	}

	public function assertAlbumLinkNotVisible(BethinkBrowser $browser)
	{
		$browser
			->on(new MyOrdersPage)
			->assertMissing('@album-order-link');
	}

	public function initiateAlbumOrder(BethinkBrowser $browser)
	{
		$browser
			->on(new MyOrdersPage)
			->click('@album-order-link');
	}
}
