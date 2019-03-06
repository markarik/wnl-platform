<?php

namespace Tests\Browser\Pages\Course;

use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Page as BasePage;
use PHPUnit\Framework\Assert as PHPUnit;
use Tests\BethinkBrowser;

class Course extends BasePage
{

	const TEMPLATE_NTH_LESSON_ANCHOR = '.items .item:nth-of-type(%d) a';
	const TEMPLATE_NTH_LESSON_SELECTOR = '.items .item:nth-of-type(%d)';
	const LESSON_OFFSET = 1;

	public function url()
	{
		return '/app/courses/1/';
	}


	/**
	 * Get the element shortcuts for the page.
	 *
	 * @return array
	 */
	public function elements()
	{
		return [
			'@welcome_message_container' => '.scrollable-main-container',
			'@start_first_lesson_button' => 'a[href="/app/courses/1/lessons/1"]',
			'@side_nav' => '.course-sidenav'
		];
	}

	public function startFirstLesson(BethinkBrowser $browser)
	{
		$browser->click('@start_first_lesson_button');
	}

	public function waitTillLoaded(BethinkBrowser $browser)
	{
		$browser->waitFor('.scrollable-main-container', 15);
	}

	public function goToLesson(BethinkBrowser $browser, $lessonIndex)
	{
		$driver = $browser->driver;
		$lessonSelector = sprintf(self::TEMPLATE_NTH_LESSON_ANCHOR, $lessonIndex + self::LESSON_OFFSET);
		$lesson = $driver->findElement(WebDriverBy::cssSelector($lessonSelector));
		$lesson->click();
	}

	public function assertExpectedLessonMarked(BethinkBrowser $browser, $lessonIndex)
	{
		$driver = $browser->driver;
		$lessonSelector = sprintf(self::TEMPLATE_NTH_LESSON_SELECTOR, $lessonIndex + self::LESSON_OFFSET);
		$lesson = $driver->findElement(WebDriverBy::cssSelector($lessonSelector));

		PHPUnit::assertTrue(strpos($lesson->getAttribute('class'), 'complete') !== false);
	}
}
