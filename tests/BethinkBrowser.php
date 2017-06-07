<?php


namespace Tests;

use Facebook\WebDriver\Exception\NoSuchElementException;
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

		return $this;
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

	/**
	 * Wait for all the given texts to be visible.
	 *
	 * @param array $items
	 * @param  int $seconds
	 * @return $this
	 * @internal param string $text
	 */
	public function waitForAll(array $items, $seconds = 5)
	{
		foreach ($items as $item) {
			$this->waitForText($item, $seconds);
		}

		return $this;
	}

	public function executeScript($script, $arguments)
	{
		return $this->driver->executeScript($script, $arguments);
	}

	public function switchToIframeBySrc($src)
	{
		$selector = sprintf('iframe[src*="%s"]', $src);
		$this->waitFor($selector);
		$iframeElement = $this->driver->findElement(WebDriverBy::cssSelector($selector));
		$this->driver->switchTo()->frame($iframeElement);
	}

	public function switchToMainWindow()
	{
		$this->driver->switchTo()->defaultContent();
	}

	public function elementPresent($selector)
	{
		try {
			$this->driver->findElement(WebDriverBy::cssSelector($selector));
		} catch (NoSuchElementException $e) {
			return false;
		}

		return true;
	}

	public function scrollTo($selector)
	{
		$this->executeScript('return document.querySelector(arguments[0]).scrollIntoView(true)', [$selector]);
		return $this;
	}
}
