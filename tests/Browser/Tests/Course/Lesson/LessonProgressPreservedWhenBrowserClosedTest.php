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
			$this->markTestSkipped(
				'Only run this test when redis is clean - TODO figure out how to clear user progress before test'
			);
			$LESSON_COMPLETED = 1;
			$LAST_SECTION = 2;

			$browser
				->visit(new Login())
				->loginAsUser($email, $password)
				//TODO this is needed until we implement progress state in localStorage better
				->clearUserData()
				->on(new Course())
				->waitFor('@side_nav', 15)
				->goToLesson($LESSON_COMPLETED)
				->on(new Lesson())
				->waitFor('@side_nav', 15)
				->goToSection($LAST_SECTION)
				->assertExpectedSectionActive($LAST_SECTION)
				->quit();

			$secondBrowser
				->visit(new Login())
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
