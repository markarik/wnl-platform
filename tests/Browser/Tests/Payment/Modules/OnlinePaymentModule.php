<?php


namespace Tests\Browser\Tests\Payment\Modules;


use Tests\BethinkBrowser;
use Tests\Browser\Pages\Payment\P24ChooseBank;

class OnlinePaymentModule
{
	public function successfulPayment(BethinkBrowser $browser)
	{
		$this->payment($browser);
		$browser->press('@confirm-payment');
	}

	public function rejectedPayment(BethinkBrowser $browser)
	{
		$this->payment($browser);
		$browser->press('@decline-payment');
		$browser->click('@return');
	}

	protected function payment(BethinkBrowser $browser)
	{
		$browser
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
