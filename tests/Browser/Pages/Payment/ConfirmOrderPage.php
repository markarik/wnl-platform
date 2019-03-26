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
			'@payment-method-now' => '#paymentMethodNow',
			'@payment-method-later' => '#paymentMethod7days',

			'@select-instalments' => '#instalments',
			'@open-instalments-modal' => '#instalments-modal-opener',
			'@close-instalments-modal' => '#instalments-modal-closer',
			'@instalments-total-amount' => '.o-checkoutInstalmentsModal .m-checkoutTotalAmount',

			'@edit-personal-data' => '[data-link="edit-personal-data"]',
			'@cart' => '.cart',
			'@submit-confirm-order' => '#confirmOrderSubmit',
		];
	}
}
