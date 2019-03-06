<?php


namespace Tests\Browser\Tests\Payment\Modules;


use Tests\BethinkBrowser;
use Tests\Browser\Pages\Payment\ConfirmOrderPage;

class ConfirmOrderModule
{
	public function editData(BethinkBrowser $browser)
	{
		$browser->on(new ConfirmOrderPage);
		$browser->click('@edit-persona-data');
	}

	public function payByTransfer(BethinkBrowser $browser)
	{
		$browser->on(new ConfirmOrderPage);
		$this->pay($browser, 'transfer');
	}

	public function payOnline(BethinkBrowser $browser)
	{
		$browser->on(new ConfirmOrderPage);
		$this->pay($browser, 'online');
	}

	public function payByInstalments(BethinkBrowser $browser)
	{
		$browser->on(new ConfirmOrderPage);
		$this->pay($browser, 'instalments');
	}

	protected function pay(BethinkBrowser $browser, $method)
	{
		$this->checkOrder($browser);
		if (intval($browser->order->total_with_coupon) === 0) {
			$browser->xpathClick('.//button[1]');

			return;
		}

		if ($method === 'instalments') {
			$browser
				->click('@expand-instalments')
				->pause(500)
				->press('@instalments-button');

			return;
		}

		if ($method === 'transfer') {
			$browser->xpathClick('.//button[1]');

			return;
		}

		if ($method === 'online') {
			$browser
				->press('#p24-submit-full-payment')
				->waitFor('a[data-search="Płać z ING 112"]', 100);
		}
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
			$coupon = $browser->coupon;
			$browser->assertSee($coupon->name);
		}
	}
}
