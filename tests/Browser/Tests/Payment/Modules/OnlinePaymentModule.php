<?php


namespace Tests\Browser\Tests\Payment\Modules;


use Tests\BethinkBrowser;
use Tests\Browser\Pages\Payment\P24ChooseBank;

class OnlinePaymentModule
{
	public function successfulPayment(BethinkBrowser $browser, string $expectedAmount)
	{
		$this->payment($browser, $expectedAmount);
		$browser->press('@confirm-payment');
	}

	protected function payment(BethinkBrowser $browser, string $expectedAmount)
	{
		$browser
			->waitUntil('window.origin === "https://sandbox.przelewy24.pl"', 60)
			->assertSee('Kwota: ' . $expectedAmount . ' PLN')
			->on(new P24ChooseBank)
			->click('@ing-logo')
			->waitForReload();

		// On second payment user is already logged in
		if ($browser->getCurrentPath() === '/pl/login') {
			$browser
				->waitFor('@login-button')
				->press('@login-button');
		}
	}
}
