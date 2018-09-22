<?php

namespace Tests\Browser\Pages\Course\Components;

use Laravel\Dusk\Page as BasePage;

//@TODO Navigation should be a component which is injected into Page Objects
// consider some DI library
class Navigation extends BasePage
{
	public function elements()
	{
		return [
			'@dropdown_trigger' => '.wnl-navbar-profile .activator',
			'@logout' => 'a[href="/logout"]',
			'@dropdown_username' => '.wnl-dropdown .box.drawer .metadata'
		];
	}

	public function logoutUser($browser)
	{
		$browser->waitUntilMissing('.wnl-overlay');
		$browser->click('@dropdown_trigger');
		$browser->click('@logout');
	}

	public function url()
	{
		// navigation doesn't have it's own URL
	}

	public function assertUserLoggedIn($browser, $userName) {
		$browser
			->click('@dropdown_trigger')
			->assertSeeIn('@dropdown_username', strtoupper($userName));
	}
}
