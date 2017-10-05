<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class LoginModal extends BasePage
{
	/**
	 * Get the URL for the page.
	 *
	 * @return string
	 */
	public function url()
	{
		return '/login';
	}

	/**
	 * Get the element shortcuts for the page.
	 *
	 * @return array
	 */
	public function elements()
	{
		return [
			'@email_input' => '#login-modal #email',
			'@password_input' => '#login-modal #password',
			'@submit_button' => '#login-modal .wnl-login-form button[type="submit"]',
		];
	}

	public function loginAsUser(Browser $browser, $email, $password)
	{
		$browser
			->type('@email_input', $email)
			->type('@password_input', $password)
			->click('@submit_button');
	}
}
