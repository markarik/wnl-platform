<?php

namespace Tests\Browser\Pages\Payment;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class ConfirmOrderPage extends BasePage
{
	/**
	 * Get the URL for the page.
	 *
	 * @return string
	 */
	public function url()
	{
		return '/payment/confirm-order';
	}

	/**
	 * Assert that the browser is on the page.
	 *
	 * @return void
	 */
	public function assert(Browser $browser)
	{
		$browser->assertPathIs($this->url());
	}

	/**
	 * Get the element shortcuts for the page.
	 *
	 * @return array
	 */
	public function elements()
	{
		return [
			'@pay-online-now' => '[data-button="pay-online-now"]',
			'@pay-online-later' => '[data-button="pay-online-later"]',
			'@pay-instalments-now' => '[data-button="pay-instalments-now"]',
			'@pay-instalments-later' => '[data-button="pay-instalments-later"]',
			'@pay-free' => '[data-button="pay-free"]',
			'@edit-personal-data' => '.edit-personal-data a',
			'@expand-instalments' => '#expand-instalments',
			'@cart' => '.cart',
			'@p24-ing' => 'a[data-search="Płać z ING 112"]'
		];
	}
}
