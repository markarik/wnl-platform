<?php


namespace Tests\Browser\Tests\Payment\Modules;


use Tests\Browser\Pages\Payment\SelectProductPage;

class SelectProductModule
{
	public function online($browser)
	{
		$browser
			->visit(new SelectProductPage)
			->click('@online-button');

		if (empty($browser->user)) {
			return PersonalDataModule::class;
		} else {
			return ConfirmOrderModule::class;
		}
	}

	public function onsite($browser)
	{
		$browser
			->visit(new SelectProductPage)
			->click('@onsite-button');

		if (empty($browser->user)) {
			return PersonalDataModule::class;
		} else {
			return ConfirmOrderModule::class;
		}
	}
}
