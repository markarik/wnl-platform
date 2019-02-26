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
			intval($config['default_position']['x']),
			intval($config['default_position']['y'])
		);

		$this->resize(
			intval($config[$config['screen-size']]['width']),
			intval($config[$config['screen-size']]['height'])
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

	public function scrollTo($x, $y)
	{
		$this->driver->executeScript("window.scroll({$x} - window.innerWidth/2, {$y} - window.innerHeight/2)");

		return $this;
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
	 * Scroll browser window one page up
	 */
	public function pageUp()
	{
		$this->driver->executeScript('window.scrollBy(0, -window.innerHeight)');

		return $this;
	}

	/**
	 * Scroll browser window to top
	 */
	public function scrollTop()
	{
		$this->driver->executeScript('window.scrollBy(0, -100000000)');

		return $this;
	}

	/**
	 * Find element by xpath
	 *
	 * @param $pattern
	 *
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
	 *
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
	 *
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
		}
		catch (NoSuchElementException $e) {
			return false;
		}

		return true;
	}

	public function scrollToSelector($selector)
	{
		$this->executeScript('return document.querySelector(arguments[0]).scrollIntoView(true)', [$selector]);

		return $this;
	}

	public function scrollToElement($element)
	{
		$location = $element->getLocation();
		$this->scrollTo($location->getX(), $location->getY());

		return $this;
	}

	public function getCurrentPath()
	{
		return parse_url(
			$this->driver->getCurrentURL()
		)['path'];
	}

	public function check($field, $value = null)
	{
		$element = $this->resolver->resolveForChecking($field, $value);
		$this->scrollToElement($element);

		if (!$element->isSelected()) {
			$element->click();
		}

		return $this;
	}

	public function click($selector = null)
	{
		$element = $this->resolver->findOrFail($selector);
		$this->scrollToElement($element);
		$element->click();

		return $this;
	}

	public function press($button)
	{
		$element = $this->resolver->resolveForButtonPress($button);
		$this->scrollToElement($element);
		$element->click();

		return $this;
	}

	public function xpathClick($pattern)
	{
		$element = $this->xpath($pattern);
		$this->scrollToElement($element);
		$element->click();

		return $this;
	}
}
