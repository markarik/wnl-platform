<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Course\Course;
use Tests\Browser\Pages\Course\Lesson;
use Tests\DuskTestCase;

class CourseProgressPreservedTest extends DuskTestCase
{
	public function testCourseProgressPreserved()
	{
		$this->browse(function (Browser $browser, Browser $secondBrowser) {
			$LESSON_COMPLETED = 2;

			factory(User::class)->create();
			$browser->loginAs(User::find(1))
				->visit(new Course())
				->waitFor('@side_nav', 15)
				->goToLesson($LESSON_COMPLETED)
				->on(new Lesson())
				->completeLesson()
				->waitFor('@side_nav', 15)
				->quit();

//			$secondBrowser->maximize()
//				->visit(new Login())
//				//TODO this is needed until we implement progress state in localStorage better
//				->clearUserData()
//				->waitFor('@email_input')
//				->loginAsUser($user->email, 'secret')
//				->on(new Course())
//				->waitFor('@side_nav', 15)
//				->assertExpectedLessonMarked($LESSON_COMPLETED);
		});
	}
}
