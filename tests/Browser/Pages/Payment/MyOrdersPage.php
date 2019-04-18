<?php

namespace Tests\Browser\Pages\Payment;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class MyOrdersPage extends BasePage
{
	/**
	 * Get the URL for the page.
	 *
	 * @return string
	 */
	public function url()
	{
		return '/app/myself/orders';
	}

	/**
	 * Assert that the browser is on the page.
	 *
	 * @param Browser $browser
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
			'@discounts-tab' => '.fa-gift',
			'@add-discount' => '[data-button="add-coupon"]',
			'@use' => '[data-button="coupon-submit"]',
			'@album-order-link' => '[data-button="order-album"]',
			'@loading-overlay' => '.wnl-overlay'
		];
	}
}
