<?php

namespace Tests\Browser\Pages\Payment;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;
use PHPUnit\Framework\Assert;

class P24ChooseBank extends BasePage
{
	/**
	 * Get the URL for the page.
	 *
	 * @return string
	 */
	public function url()
	{
		return 'https://sandbox.przelewy24.pl/trnRequest';
	}

	/**
	 * Assert that the browser is on the page.
	 *
	 * @return void
	 */
	public function assert(Browser $browser)
	{
		Assert::assertStringStartsWith($this->url(), $browser->driver->getCurrentURL());
	}

	/**
	 * Get the element shortcuts for the page.
	 *
	 * @return array
	 */
	public function elements()
	{
		return [
			'@ing-logo'        => 'a[data-search="Płać z ING 112"]',
			'@accept-tou'      => 'button[id="reagulation-accept-button"]',
			'@login-button'    => 'button[type="submit"]',
			'@confirm-payment' => '#pay_by_link_pay',
			'@decline-payment' => '#pay_by_link_decline',
			'@return'          => '#btn-return',
		];
	}
}
