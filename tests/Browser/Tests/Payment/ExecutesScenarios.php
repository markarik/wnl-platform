<?php

namespace Tests\Browser\Tests\Payment;


trait ExecutesScenarios
{
	protected function execute($scenario)
	{
		$this->browse(function ($browser) use ($scenario) {
			foreach ($scenario as list($module, $method)) {
				(new $module)->$method($browser);
			}
		});
	}
}
