<?php

namespace Tests\Browser\Tests\Payment;


use Tests\BethinkBrowser;

trait ExecutesScenarios
{
	protected function execute($scenario)
	{
		try {
			$this->browse(function (BethinkBrowser $browser) use ($scenario) {
				foreach ($scenario as list($module, $method)) {
					(new $module)->$method($browser);
				}
			});
		} finally {
			// We set various properties on browser instance, so let's prevent leaking them between tests
			$this->closeAll();
		}
	}
}
