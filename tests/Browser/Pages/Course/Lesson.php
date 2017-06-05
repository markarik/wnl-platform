<?php

namespace Tests\Browser\Pages\Course;

use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Page as BasePage;
use PHPUnit\Framework\Assert as PHPUnit;

class Lesson extends BasePage
{
	private $slideContent;

	/**
	 * Get the URL for the page.
	 *
	 * @return string
	 */
	public function url()
	{
		return '/app/courses/1/lessons/1/screens/1';
	}


	/**
	 * Get the element shortcuts for the page.
	 *
	 * @return array
	 */
	public function elements()
	{
		return [
			'@navigate_right' => '.navigate-right',
			'@navigate_left' => '.navigate-left'
		];
	}

	public function switchToLessonFrame($browser)
	{
		$browser->switchToIframeBySrc('http://platforma.wnl/slideshow-builder/1');
		$this->slideContent = $this->getSlideContent($browser);
	}

	public function nextSlide($browser)
	{
		$browser->click('@navigate_right');
	}

	public function assertNextSlide($browser) {
		$browser->waitFor('@navigate_left');
		$nextSlideContent = $this->getSlideContent($browser);
		PHPUnit::assertNotEquals($this->slideContent, $nextSlideContent);
	}

	private function getSlideContent($browser)
	{
		return $browser->driver->findElement(WebDriverBy::cssSelector('.present'))->getAttribute('innerHTML');
	}
}
