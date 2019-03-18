<?php

namespace Tests\Browser\Tests\Payment;


use Tests\BethinkBrowser;

trait ExecutesScenarios
{
	protected function execute($scenario)
	{
		$this->browse(function (BethinkBrowser $browser) use ($scenario) {
			foreach ($scenario as $scenarioArgs) {
				$module = $scenarioArgs[0];
				$method = $scenarioArgs[1];
				$methodArgs = $scenarioArgs[2] ?? [];

				array_unshift($methodArgs, $browser);
				call_user_func_array([(new $module), $method], $methodArgs);
			}
		});
	}
}
