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
			'@online-payment-button' => 'button.p24-submit',
			'@edit-persona-data'     => '.edit-personal-data a',
			'@expand-instalments'    => '#expand-instalments',
			'@instalments-button'    => '#instalments-button',
			'@cart' => '.cart'
		];
	}
}
