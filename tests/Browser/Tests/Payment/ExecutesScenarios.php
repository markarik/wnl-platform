<?php

namespace Tests\Browser\Tests\Payment;


use Tests\BethinkBrowser;

trait ExecutesScenarios
{
	protected function execute($scenario)
	{
		$this->browse(function (BethinkBrowser $browser) use ($scenario) {
			foreach ($scenario as list($module, $method)) {
				(new $module)->$method($browser);
			}
		});
		$this->closeAll();
	}
}
