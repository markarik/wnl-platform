<?php


namespace Tests\Browser\Tests\Payment\Modules;


use Tests\BethinkBrowser;
use Tests\Browser\Pages\Payment\ConfirmOrderPage;

class ConfirmOrderModule
{
	const METHOD_ONLINE_NOW = 'onlineNow';
	const METHOD_ONLINE_LATER = 'onlineLater';
	const METHOD_INSTALMENTS_LATER = 'instalmentsLater';
	const METHOD_INSTALMENTS_NOW = 'instalmentsNow';

	public function editData(BethinkBrowser $browser)
	{
		$browser->on(new ConfirmOrderPage);
		$browser->click('@edit-personal-data');
	}

	public function payOnlineNow(BethinkBrowser $browser)
	{
		$this->payNow($browser);
		$browser->on(new ConfirmOrderPage)
			->press('@pay-online-now')
			->waitFor('@p24-ing', 60);
	}

	public function payOnlineLater(BethinkBrowser $browser)
	{
		$this->payLater($browser);
		$browser->on(new ConfirmOrderPage)
			->press('@pay-online-later');
	}

	public function payByInstalmentsNow(BethinkBrowser $browser)
	{
		$this->payNow($browser);
		$browser->on(new ConfirmOrderPage)
			->click('@expand-instalments')
			->pause(500);

		$this->assertInstalments($browser);

		$browser->press('@pay-instalments-now')
			->waitFor('@p24-ing', 60);
	}

	public function payByInstalmentsLater(BethinkBrowser $browser)
	{
		$this->payLater($browser);
		$browser->on(new ConfirmOrderPage)
			->click('@expand-instalments')
			->pause(500);

		$this->assertInstalments($browser);

		$browser->press('@pay-instalments-later');
	}

	public function payByCoupon100Percent(BethinkBrowser $browser)
	{
		$this->payNow($browser);
		$browser->on(new ConfirmOrderPage)
			->press('@pay-free');
	}

	protected function payNow(BethinkBrowser $browser)
	{
		$this->checkOrder($browser);
		$this->assertCart($browser);
		$browser->payLater = false;
	}

	protected function payLater(BethinkBrowser $browser)
	{
		$this->checkOrder($browser);
		$this->assertCart($browser);
		$browser->payLater = true;
	}

	protected function checkOrder(BethinkBrowser $browser)
	{
		if (empty($browser->order)) {
			$browser->order = $browser->user->orders()->recent();
		}

		$user = $browser->user;
		$order = $browser->order;
		$product = $order->product;

		$browser
			->assertSee($user->email)
			->assertSee($product->name)
			->assertSee($order->total_with_coupon);

		if (!empty($browser->coupon)) {
			$browser->assertSee($browser->coupon->name);
			$this->assertCouponInCart($browser);
		}
	}

	protected function assertCart(BethinkBrowser $browser) {
		$browser->assertVisible('@cart');
		$browser->assertSeeIn('@cart', 'Wysyłka:');
		$browser->assertSeeIn('@cart', 'Na terenie Polski za darmo');
		$browser->assertSeeIn('@cart', 'Dostęp od momentu wpłaty do');
		$browser->assertSeeIn('@cart', 'Kwota całkowita:');
	}

	protected function assertCouponInCart(BethinkBrowser $browser) {
		$browser->assertSeeIn('@cart', 'Zniżka:');
	}

	private function assertInstalments(BethinkBrowser $browser)
	{
		if (!empty($browser->coupon) && $browser->coupon->is_percentage && $browser->coupon->value === 10) {
			$firstInstalmentAmount = '675zł';
			$secondInstalmentAmount = '337.5zł';
			$thirdInstalmentAmount = '337.5zł';
			$totalAmount = '1350zł';
		} else {
			$firstInstalmentAmount = '750zł';
			$secondInstalmentAmount = '375zł';
			$thirdInstalmentAmount = '375zł';
			$totalAmount = '1500zł';
		}

		$browser->assertSeeIn('#instalments-amounts td:nth-child(2)', $firstInstalmentAmount);
		$browser->assertSeeIn('#instalments-amounts td:nth-child(3)', $secondInstalmentAmount);
		$browser->assertSeeIn('#instalments-amounts td:nth-child(4)', $thirdInstalmentAmount);
		$browser->assertSeeIn('#instalments-amounts td:nth-child(5)', $totalAmount);
	}
}
