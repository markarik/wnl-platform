<?php


namespace Tests\Browser\Tests\Payment\Modules;

use App;
use App\Models\Coupon;
use PHPUnit\Framework\Assert;
use Tests\BethinkBrowser;
use Tests\Browser\Pages\Payment\MyOrdersPage;

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

	public function payNow(BethinkBrowser $browser)
	{
		$order = $browser->order;
		$browser->click($this->getContainerSelector($order) . ' [data-button="pay-now"]');
	}

	public function payNextInstalment(BethinkBrowser $browser)
	{
		$order = $browser->order;
		$browser->click($this->getContainerSelector($order) . ' [data-button="pay-next-instalment"]');
	}

	public function useCoupon(BethinkBrowser $browser, $value, $type = 'percentage') {
		$coupon = factory(Coupon::class)->create([
			'type' => $type,
			'value' => $value
		]);

		$browser->coupon = $coupon;

		$browser
			->visit(new MyOrdersPage())
			->waitUntilMissing('@loading-overlay')
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

		if (!empty($browser->coupon)) {
			$coupon = $browser->coupon;
			$browser->waitForText($coupon->name);
		}
	}

	public function assertPaid(BethinkBrowser $browser, string $expectedAmount)
	{
		$browser->waitForText('Wpłacono ' . $expectedAmount, 60);
	}

	public function assertPriceHigherAfterCouponError(BethinkBrowser $browser)
	{
		$browser->waitForText('W trosce o przyszłość kolejnych edycji zniżki nie łączą się', 60);
	}

	public function assertNotPaid(BethinkBrowser $browser)
	{
		$order = $browser->order;
		$browser->assertSeeIn($this->getContainerSelector($order) . ' .current-payment', 'KWOTA DO ZAPŁATY: 1500ZŁ');
	}

	public function assertInstalment(BethinkBrowser $browser, int $instalmentNumber, string $expectedText)
	{
		$order = $browser->order;

		$browser
			->waitForText('Zamówienie numer ' . $order->id)
			->assertSeeIn($this->getContainerSelector($order) . ' .instalment-amount[data-instalment="' . $instalmentNumber . '"]', $expectedText);
	}

	public function assertStudyBuddyCodeActive(BethinkBrowser $browser)
	{
		$order = $browser->order;
		$containerSelector = $this->getContainerSelector($order);

		$browser
			->waitForText('Zamówienie numer ' . $order->id)
			->assertSeeIn($containerSelector, 'Możesz teraz skorzystać z promocji Study Buddy')
			->assertElementNotEmpty("$containerSelector .code");
	}

	public function assertStudyBuddyCodeNotActive(BethinkBrowser $browser)
	{
		$order = $browser->order;
		$containerSelector = $this->getContainerSelector($order);

		$browser
			->waitForText('Zamówienie numer ' . $order->id)
			->assertDontSeeIn($containerSelector, 'Możesz teraz skorzystać z promocji Study Buddy');

		$studyBuddy = $browser->order->studyBuddy;

		if ($studyBuddy) {
			$studyBuddy = $studyBuddy->fresh();
			Assert::assertNotEquals('active', $studyBuddy->status);
		}
	}

	public function assertStudyBuddyAwaitingRefund(BethinkBrowser $browser)
	{
		$order = $browser->order;

		$browser
			->refresh()
			->waitForText('Zamówienie numer ' . $order->id)
			->assertSeeIn($this->getContainerSelector($order), 'Twój Study Buddy dołączył już do kursu!');
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

	public function getRefund(BethinkBrowser $browser, int $amount)
	{
		$order = $browser->order;
		$order->refresh();
		// That's what we do in InvoiceIncorrect command
		$order->paid_amount -= $amount;
		$order->save();
		$browser->refresh();
	}

	private function getContainerSelector($order)
	{
		return '.order[data-order-id="' . $order->id . '"]';
	}
}
