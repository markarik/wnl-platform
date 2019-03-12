<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Course\Course;
use Tests\Browser\Pages\Course\Lesson;
use Tests\DuskTestCase;

class CourseProgressPreservedTest extends DuskTestCase
{
	private $user;

	public function setUp(): void
	{
		parent::setUp();
		$this->user = factory(User::class)->create();
	}

	public function testCourseProgressPreserved()
	{
		$this->browse(function (Browser $browser, Browser $secondBrowser) {
			$LESSON_COMPLETED = 1;

			$browser->loginAs($this->user)
				->visit(new Course())
				->waitFor('@side_nav', 15)
				->goToLesson($LESSON_COMPLETED)
				->on(new Lesson())
				->completeLesson()
				->waitFor('@side_nav', 15)
				->quit();

			$secondBrowser->loginAs($this->user)
				->visit(new Course())
				->waitFor('@side_nav', 15)
				->assertExpectedLessonMarked($LESSON_COMPLETED);
		});
	}

	public function tearDown()
	{
		parent::tearDown();
		$this->user->delete();
	}
}
