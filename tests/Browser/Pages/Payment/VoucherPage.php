<?php

namespace Tests\Browser\Pages\Payment;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class VoucherPage extends BasePage
{
	/**
	 * Get the URL for the page.
	 *
	 * @return string
	 */
	public function url()
	{
		return '/payment/voucher';
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
			'@skip' => '[data-link="voucher-skip"]',
			'@use' => '[data-button="voucher-submit"]'
		];
	}
}
