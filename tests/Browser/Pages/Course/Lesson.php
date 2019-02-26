<?php

namespace Tests\Browser\Pages\Course;

use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Page as BasePage;
use PHPUnit\Framework\Assert as PHPUnit;
use Tests\Browser\Lib\Wait;

class Lesson extends BasePage
{
	const CSS_NAVIGATE_RIGHT = '.wnl-slideshow-control.navigate-right.enabled';
	const CSS_NAVIGATE_LEFT = '.wnl-slideshow-control.navigate-left.enabled';
	const CSS_SECTIONS = '.items .subitem a';
	const CSS_SECTIONS_VISITED = '.items .subitem a.is-completed';

	const TEMPLATE_NTH_SECTION_SELECTOR = '.items .subitem:nth-of-type(%d) a';
	const SECTIONS_OFFSET = 2;
	const TEMPLATE_SLIDE_URL = '/app/courses/1/lessons/1/screens/1/%d';

	private $slideContent;
	private $slide;
	private $lessonId;

	public function __construct($lessonId = 1)
	{
		$this->lessonId = $lessonId;
	}

	/**
	 * Get the URL for the page.
	 *
	 * @return string
	 */
	public function url()
	{
		return '/app/courses/1/lessons/' . $this->lessonId;
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
			'@side_nav' => '.course-sidenav',
			'@loading_overlay' => '.wnl-overlay'
		];
	}

	public function switchToLessonFrame($browser)
	{
		$browser->switchToIframeBySrc('/slideshow-builder/1');
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

	public function completeLesson($browser) {
		$sections = $browser->driver->findElements(WebDriverBy::cssSelector(self::CSS_SECTIONS));

		for ($i = 0; $i < count($sections); $i++) {
			$section = $sections[$i];
			$section->click();
			$this->assertSectionActive($section);
		}
	}

	public function goThroughSections($browser)
	{
		$sections = $browser->driver->findElements(WebDriverBy::cssSelector(self::CSS_SECTIONS));

		$this->switchToLessonFrame($browser);
		$this->slide = $this->getPresentSlide($browser);
		$this->slideContent = $this->getSlideContent($browser);
		$browser->switchToMainWindow();

		for ($i = 0; $i < count($sections); $i++) {
			$section = $sections[$i];
			$section->click();
			$this->assertSectionActive($section);
			$this->switchToLessonFrame($browser);

			if ($i === 0) {
				$currentSlideContent = $this->getSlideContent($browser);
				$this->assertSlideContentExpected($currentSlideContent);
			} else {
				$currentSlideContent = $this->getSlideContent($browser);
				$this->assertSlideContentChanged($currentSlideContent);
				$this->slide = $this->getPresentSlide($browser);
				$this->slideContent = $currentSlideContent;
			}

			$browser->switchToMainWindow();
		}
	}

	public function assertSectionsVisited($browser)
	{
		$numberOfSections = count($browser->driver->findElements(WebDriverBy::cssSelector(self::CSS_SECTIONS)));
		$numberOfActiveSections = count($browser->driver->findElements(WebDriverBy::cssSelector(self::CSS_SECTIONS_VISITED)));

		PHPUnit::assertTrue($numberOfSections === $numberOfActiveSections);
	}

	public function goToSection($browser, $sectionIndex)
	{
		$section = $this->findSectionByIndex($browser, $sectionIndex);
		$section->click();
	}

	public function assertExpectedSectionActive($browser, $sectionIndex)
	{
		$section = $this->findSectionByIndex($browser, $sectionIndex);
		$this->assertSectionActiveByRoute($browser, $section);
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

	private function assertSectionActiveByRoute($browser, $section)
	{
		$elementPresent = Wait::waitForElementHasClass($browser->driver, $section, 'router-link-exact-active');
		PHPUnit::assertTrue($elementPresent, 'Expected class not present in element');
	}


	private function assertSlideContentChanged($nextSlideContent)
	{
		PHPUnit::assertNotEquals($this->slideContent, $nextSlideContent);
	}

	private function assertSlideContentExpected($nextSlideContent)
	{
		PHPUnit::assertEquals($this->slideContent, $nextSlideContent);
	}

	private function findSectionByIndex($browser, $index)
	{
		$sectionSelector = sprintf(self::TEMPLATE_NTH_SECTION_SELECTOR, $index + self::SECTIONS_OFFSET);
		return $browser->driver->findElement(WebDriverBy::cssSelector($sectionSelector));
	}
}
