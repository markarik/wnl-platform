<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Course\Course;
use Tests\Browser\Pages\Course\Lesson;
use Tests\DuskTestCase;

class LessonProgressPreservedWhenBrowserClosedTest extends DuskTestCase
{
	private $user;

	public function setUp(): void
	{
		parent::setUp();
		$this->user = factory(User::class)->create();
	}

	public function testLessonProgressPreservedWhenBrowserClosed()
	{
		$this->browse(function (Browser $browser, Browser $secondBrowser) {
			$LESSON_COMPLETED = 1;
			$LAST_SECTION = 2;

			$browser->loginAs($this->user)
				->visit(new Course())
				->waitFor('@side_nav', 15)
				->goToLesson($LESSON_COMPLETED)
				->on(new Lesson())
				->waitFor('@side_nav', 15)
				->goToSection($LAST_SECTION)
				->assertExpectedSectionActive($LAST_SECTION)
				->quit();

			$secondBrowser->loginAs($this->user)
				->visit(new Course())
				->waitFor('@side_nav', 15)
				->goToLesson($LESSON_COMPLETED)
				->on(new Lesson())
				->waitFor('@side_nav', 15)
				->assertExpectedSectionActive($LAST_SECTION);
		});
	}

	public function tearDown()
	{
		parent::tearDown();
		$this->user->delete();
	}
}
