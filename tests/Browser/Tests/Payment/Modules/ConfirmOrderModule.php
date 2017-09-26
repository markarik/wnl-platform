<?php


namespace Tests\Browser\Tests\Payment\Modules;


use Tests\Browser\Pages\Payment\ConfirmOrderPage;

class ConfirmOrderModule
{
	public function editData($browser)
	{
		$browser
			->on(new ConfirmOrderPage)
			->pageDown()
			->click('@edit-persona-data');

		return PersonalDataModule::class;
	}

	public function payByTransfer($browser)
	{
		$browser
			->on(new ConfirmOrderPage)
			->pageDown()
			->xpath('.//button[1]')->click();

		return MyOrdersModule::class;
	}

	public function payOnline($browser)
	{
		$browser
			->on(new ConfirmOrderPage)
			->pageDown()
			->press('button.p24-submit')
			->waitForText('Wybierz formę płatności', 30);

		return OnlinePaymentModule::class;
	}

	public function payByInstalments($browser)
	{
		$browser
			->on(new ConfirmOrderPage)
			->pageDown()
			->click('@expand-instalments')
			->pause(500)
			->pageDown()
			->press('@instalments-button');

		return MyOrdersModule::class;
	}
}
