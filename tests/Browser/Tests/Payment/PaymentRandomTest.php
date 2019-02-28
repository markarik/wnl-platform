<?php

namespace Tests\Browser\Tests\Payment;

use Tests\BethinkBrowser;
use Tests\Browser\Tests\Payment\Modules\UserModule;
use Tests\DuskTestCase;

class PaymentRandomTest extends DuskTestCase
{
	/** @test */
	public function randomCheckoutTest()
	{
		if (file_exists('scenario.dusk')) unlink('scenario.dusk');
		$this->browse(function (BethinkBrowser $browser) {
			$next = UserModule::class;
			while ($next) $next = $this->callRandom($next, $browser);
		});
	}

	protected function callRandom($next, $browser)
	{
		if (is_array($next)) {
			$next = array_random($next);
		}
		$methods = get_class_methods($next);
		if (empty($methods)) {
			return false;
		}
		$method = array_random($methods);

		$action = class_basename($next) . '->' . $method . PHP_EOL;
		file_put_contents('scenario.dusk', $action, FILE_APPEND);

		return (new $next)->$method($browser);
	}

	/** @test */
	public function fromScenarioFile()
	{
		if (!file_exists('scenario.dusk')) {
			print 'File scenario.dusk not found!';
		}

		$this->browse(function (BethinkBrowser $browser) {
			$contents = file_get_contents('scenario.dusk');
			$namespace = 'Tests\Browser\Tests\Payment\Modules\\';
			foreach (explode("\n", $contents) as $item) {
				if (!$item) continue;
				fwrite(STDOUT, $item . PHP_EOL);
				list($module, $method) = explode('->', $item);
				$module = $namespace . $module;
				(new $module)->$method($browser);
			}
		});
	}
}
