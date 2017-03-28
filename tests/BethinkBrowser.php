<?php


namespace Tests;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverPoint;
use \Laravel\Dusk\Browser;
use Laravel\Dusk\ElementResolver;

class BethinkBrowser extends Browser
{
	/**
	 * Create a browser instance.
	 *
	 * @param  \Facebook\WebDriver\Remote\RemoteWebDriver $driver
	 * @param  ElementResolver $resolver
	 */
	public function __construct($driver, $resolver = null)
	{
		parent::__construct($driver, $resolver = null);

		$config = config('dusk');

		$this->position(
			$config['default_position']['x'],
			$config['default_position']['y']
		);
		$this->resize(
			$config['default_size']['width'],
			$config['default_size']['height']
		);
	}

	/**
	 * Set window position
	 *
	 * @param $x
	 * @param $y
	 */
	public function position($x, $y)
	{
		$this->driver->manage()->window()->setPosition(
			new WebDriverPoint($x, $y)
		);
	}

	/**
	 * Scroll browser window one page down
	 */
	public function pageDown()
	{
		$this->driver->executeScript('window.scrollBy(0, window.innerHeight)');
	}

	/**
	 * Find element by xpath
	 *
	 * @param $pattern
	 * @return \Facebook\WebDriver\Remote\RemoteWebElement
	 */
	public function xpath($pattern)
	{
		return $this->driver->findElement(WebDriverBy::xpath($pattern));
	}

	/**
	 * Assert that all the given texts appear on the page.
	 *
	 * @param array $items
	 * @return $this
	 */
	public function assertSeeAll(array $items)
	{
		foreach ($items as $item) {
			$this->assertSee($item);
		}

		return $this;
	}
}
