<?php

namespace Tests\Browser\Pages\Payment;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;
use PHPUnit\Framework\Assert;

class PersonalDataPage extends BasePage
{
	/**
	 * Get the URL for the page.
	 *
	 * @return string
	 */
	public function url()
	{
		return '/payment/personal-data';
	}

	/**
	 * Assert that the browser is on the page.
	 *
	 * @return void
	 */
	public function assert(Browser $browser)
	{
		Assert::assertStringStartsWith($this->url(), parse_url(
			$browser->driver->getCurrentURL()
		)['path']);
	}

	/**
	 * Get the element shortcuts for the page.
	 *
	 * @return array
	 */
	public function elements()
	{
		return [
			'@login' => '.has-account a',
			'@cart' => '.o-cart',
			'@submit-personal-data' => '[data-button="submit-personal-data"]'
		];
	}
}
