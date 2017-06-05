<?php

namespace Tests\Browser\Pages\Course;

use Laravel\Dusk\Page as BasePage;

class Course extends BasePage
{

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
			'@start_first_lesson_button' => 'a[href="/app/courses/1/lessons/1"]'
		];
	}

	public function startFirstLesson($browser)
	{
		$browser->click('@start_first_lesson_button');
	}

	public function waitTillLoaded($browser)
	{
		$browser->waitFor('.scrollable-main-container', 15);
	}
}
