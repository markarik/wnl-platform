<?php


namespace Tests\Browser\Tests\Payment\Modules;


use Tests\Browser\Pages\Payment\ConfirmOrderPage;

class ConfirmOrderModule
{
	public function editData($browser)
	{
		$browser->on(new ConfirmOrderPage);
		$browser->click('@edit-persona-data');

		return PersonalDataModule::class;
	}

	public function payByTransfer($browser)
	{
		$browser->on(new ConfirmOrderPage);
		$this->pay($browser, 'transfer');

		return MyOrdersModule::class;
	}

	public function payOnline($browser)
	{
		$browser->on(new ConfirmOrderPage);
		$this->pay($browser, 'online');

		return OnlinePaymentModule::class;
	}

	public function payByInstalments($browser)
	{
		$browser->on(new ConfirmOrderPage);
		$this->pay($browser, 'instalments');

		return MyOrdersModule::class;
	}

	protected function pay($browser, $method)
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

	protected function checkOrder($browser)
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
