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

		return MyOrdersModule::class;
	}

	public function rejectedPayment(BethinkBrowser $browser)
	{
		$this->payment($browser);
		$browser->press('@decline-payment');
		$browser->click('@return');

		return MyOrdersModule::class;
	}

	protected function payment(BethinkBrowser $browser)
	{
		$browser
			->on(new P24ChooseBank)
			->click('@ing-logo');

		$browser
			->waitFor('@login-button', 40)
			->press('@login-button');
	}
}
