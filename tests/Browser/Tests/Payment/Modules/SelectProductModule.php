<?php


namespace Tests\Browser\Tests\Payment\Modules;


use Tests\BethinkBrowser;
use Tests\Browser\Pages\Payment\SelectProductPage;

class SelectProductModule
{
	public function online(BethinkBrowser $browser)
	{
		$browser->visit(new SelectProductPage);

		$this->assertCart($browser);

		$browser->click('@online-button');
	}

	public function onsite(BethinkBrowser $browser)
	{
		$browser->visit(new SelectProductPage);

		$this->assertCart($browser);

		$browser->click('@onsite-button');
	}

	private function assertCart(BethinkBrowser $browser) {
		$browser->assertSeeIn('@cart', 'Twój koszyk jest pusty');
	}
}
