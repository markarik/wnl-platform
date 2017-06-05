<?php

namespace Tests\Browser\Pages\Course;

use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Page as BasePage;
use PHPUnit\Framework\Assert as PHPUnit;
use Tests\Browser\Library\ExpectedConditions;

class Lesson extends BasePage
{
	const CSS_NAVIGATE_RIGHT = '.navigate-right.enabled';
	const CSS_SECTIONS = '.items .subitem a';
	const CSS_NAVIGATE_LEFT = '.navigate-left.enabled';
	const CSS_SECTIONS_VISITED = '.items .subitem a.is-active';

	private $slideContent;
	private $slide;

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
			'@navigate_right' => self::CSS_NAVIGATE_RIGHT,
			'@navigate_left' => self::CSS_NAVIGATE_LEFT,
			'@sections' => self::CSS_SECTIONS,
			'@side_nav' => '.wnl-sidenav'
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

	public function assertNextSlide($browser)
	{
		$browser->waitFor('@navigate_left');
		$nextSlideContent = $this->getSlideContent($browser);
		$this->assertSlideContentChanged($nextSlideContent);
	}

	public function goThroughSlides($browser)
	{
		$hasNextSlide = $browser->elementPresent(self::CSS_NAVIGATE_RIGHT);;
		while ($hasNextSlide) {
			$this->nextSlide($browser);
			$hasNextSlide = $browser->elementPresent(self::CSS_NAVIGATE_RIGHT);
		}
	}

	public function assertLastSlide($browser)
	{
		PHPUnit::assertFalse($browser->elementPresent(self::CSS_NAVIGATE_RIGHT));
		PHPUnit::assertTrue($browser->elementPresent(self::CSS_NAVIGATE_LEFT));
	}

	public function goThroughSections($browser)
	{
		$sections = $browser->driver->findElements(WebDriverBy::cssSelector(self::CSS_SECTIONS));

		$this->slideContent = null;
		$this->slide = null;

		for ($i = 0; $i < count($sections); $i++) {
			$section = $sections[$i];
			$section->click();
			$this->assertSectionActive($section);
			$this->switchToLessonFrame($browser);

			if (empty($this->slide)) {

			} else {
				$browser->driver
					->wait(5, 200)
					->until(
						ExpectedConditions::elementContainsAttribute($this->slide, 'class', 'past')
					);
				$nextSlideContent = $this->getSlideContent($browser);
				$this->assertSlideContentChanged($nextSlideContent);
				$this->slide = $this->getPresentSlide($browser);
				$this->slideContent = $nextSlideContent;
				$browser->switchToMainWindow();
			}
			$sections = $browser->driver->findElements(WebDriverBy::cssSelector(self::CSS_SECTIONS));
		}
	}

	public function assertSectionsVisited($browser)
	{
		$numberOfSections = count($browser->driver->findElements(WebDriverBy::cssSelector(self::CSS_SECTIONS)));
		$numberOfActiveSections = count($browser->driver->findElements(WebDriverBy::cssSelector(self::CSS_SECTIONS_VISITED)));

		PHPUnit::assertTrue($numberOfSections === $numberOfActiveSections);
	}

	private function getSlideContent($browser)
	{
		return $this->getPresentSlide($browser)->getAttribute('innerHTML');
	}

	private function getPresentSlide($browser)
	{
		return $browser->driver->findElement(WebDriverBy::cssSelector('.present'));
	}

	private function assertSectionActive($section)
	{
		PHPUnit::assertTrue(strpos($section->getAttribute('class'), 'is-active') !== false);
	}


	private function assertSlideContentChanged($nextSlideContent)
	{
		PHPUnit::assertNotEquals($this->slideContent, $nextSlideContent);
	}
}
