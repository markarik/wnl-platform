<?php

namespace Tests;

use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

abstract class DuskTestCase extends BaseTestCase
{
	use CreatesApplication;

	/**
	 * Prepare for Dusk test execution.
	 *
	 * @beforeClass
	 * @return void
	 */
	public static function prepare()
	{
		static::startChromeDriver();
	}

	protected function driver(): RemoteWebDriver
	{
		$options = (new ChromeOptions)->addArguments([
//			'--disable-gpu',
//			'--headless',
			'--no-sandbox'
		]);

		return RemoteWebDriver::create(
			'http://' . config('dusk.selenium_host') . '/wd/hub', DesiredCapabilities::chrome()->setCapability(
			ChromeOptions::CAPABILITY, $options
		));
	}

	/**
	 * Create a new Browser instance.
	 *
	 * @param  \Facebook\WebDriver\Remote\RemoteWebDriver $driver
	 * @return \Laravel\Dusk\Browser
	 */
	protected function newBrowser($driver)
	{
		return new BethinkBrowser($driver);
	}
}
