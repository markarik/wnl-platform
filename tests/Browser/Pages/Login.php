<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class Login extends BasePage
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
			'@email_input' => '#email',
			'@password_input' => '#password',
			'@submit_button' => '.wnl-login-form button[type="submit"]',
			'@avatar' => '.wnl-avatar',
			'@privacy-policy-button' => '#btest-accept-privacy-policy-button'
		];
	}

	public function loginAsUser(Browser $browser, $email, $password)
	{
		$browser->type('@email_input', $email)
			->type('@password_input', $password)
			->click('@submit_button')
			->click('@privacy-policy-button')
			->waitFor('@avatar');
	}
}
