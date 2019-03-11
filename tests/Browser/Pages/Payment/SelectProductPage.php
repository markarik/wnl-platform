<?php


namespace Tests\Browser\Pages\Payment;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Page;

class SelectProductPage extends Page
{
	/**
	 * Get the URL for the page.
	 *
	 * @return string
	 */
	public function url()
	{
		return '/payment/select-product';
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
			'@onsite-button' => '#btest-wnl-online-onsite-button',
			'@online-button' => '#btest-wnl-online-button',
			'@cart' => '.cart'
		];
	}

}
