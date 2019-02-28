<?php


namespace Tests\Browser\Tests\Payment\Modules;


use Tests\Browser\Pages\Payment\P24ChooseBank;

class OnlinePaymentModule
{
	public function successfulPayment($browser)
	{
		$this->payment($browser);
		$browser->press('@confirm-payment');

		return MyOrdersModule::class;
	}

	public function rejectedPayment($browser)
	{
		$this->payment($browser);
		$browser->press('@decline-payment');
		$browser->click('@return');

		return MyOrdersModule::class;
	}

	protected function payment($browser)
	{
		$browser
			->on(new P24ChooseBank)
			->click('@ing-logo');

		$browser
			->waitFor('@login-button', 40)
			->press('@login-button');
	}
}
