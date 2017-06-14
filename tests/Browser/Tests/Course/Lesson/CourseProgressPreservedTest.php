<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Course\Course;
use Tests\Browser\Pages\Course\Lesson;
use Tests\Browser\Pages\Login;
use Tests\DuskTestCase;

class CourseProgressPreservedTest extends DuskTestCase
{
	/**
	 * @dataProvider Tests\Browser\DataProviders\User::userProvider
	 * @param String $email
	 * @param String $password
	 * @param String $name
	 */
	public function testCourseProgressPreserved($email, $password, $name)
	{
		$this->browse(function (Browser $browser, Browser $secondBrowser) use ($email, $password, $name) {
			$LESSON_COMPLETED = 2;

			$this->markTestSkipped(
				'Only run this test when redis is clean - TODO figure out how to clear user progress before test'
			);

			$browser->maximize()
				->visit(new Login())
				->loginAsUser($email, $password)
				->on(new Course())
				->waitFor('@side_nav', 15)
				->goToLesson($LESSON_COMPLETED)
				->on(new Lesson())
				->completeLesson()
				->waitFor('@side_nav', 15)
				->quit();

			$secondBrowser->maximize()
				->visit(new Login())
				//TODO this is needed until we implement progress state in localStorage better
				->clearUserData()
				->waitFor('@email_input')
				->loginAsUser($email, $password)
				->on(new Course())
				->waitFor('@side_nav', 15)
				->assertExpectedLessonMarked($LESSON_COMPLETED);
		});
	}
}
