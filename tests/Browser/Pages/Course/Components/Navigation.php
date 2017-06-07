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
			'@dropdown_trigger' => '.wnl-dropdown .activator',
			'@logout' => 'a[href="/logout"]'
		];
	}

	public function logoutUser($browser)
	{
		$browser
			->click('@dropdown_trigger')
			->click('@logout');
	}

	public function url()
	{
		// navigation doesn't have it's own URL
	}
}
