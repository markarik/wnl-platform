<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Course\Course;
use Tests\Browser\Pages\Course\Lesson;
use Tests\Browser\Pages\Login;
use Tests\DuskTestCase;

class LessonProgressPreservedWhenBrowserClosedTest extends DuskTestCase
{
	/**
	 * @dataProvider Tests\Browser\DataProviders\User::userProvider
	 * @param String $email
	 * @param String $password
	 * @param String $name
	 */
	public function testLessonProgressPreservedWhenBrowserClosed($email, $password, $name)
	{
		$this->browse(function (Browser $browser, Browser $secondBrowser) use ($email, $password, $name) {
			$LESSON_COMPLETED = 1;
			$LAST_SECTION = 2;

			$browser->maximize()
				->visit(new Login())
				->loginAsUser($email, $password)
				->on(new Course())
				->waitFor('@side_nav', 15)
				->goToLesson($LESSON_COMPLETED)
				->on(new Lesson())
				->waitFor('@side_nav', 15)
				->goToSection($LAST_SECTION)
				->quit();

			$secondBrowser->maximize()
				->visit(new Login())
				//TODO this is needed until we implement progress state in localStorage better
				->clearUserData()
				->waitFor('@email_input')
				->loginAsUser($email, $password)
				->on(new Course())
				->waitFor('@side_nav', 15)
				->goToLesson($LESSON_COMPLETED)
				->on(new Lesson())
				->waitFor('@side_nav', 15)
				->assertExpectedSectionActive($LAST_SECTION);
		});
	}
}
