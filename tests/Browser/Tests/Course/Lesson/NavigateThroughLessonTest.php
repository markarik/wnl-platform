<?php

namespace Tests\Browser;

use App\Models\User;
use Tests\Browser\Pages\Course\Lesson;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class NavigateThroughLessonTest extends DuskTestCase
{

	private $user;

	public function setUp(): void
	{
		parent::setUp();
		$this->user = factory(User::class)->create();
	}

	public function testNavigateThroughLesson()
	{
		$this->browse(function (Browser $browser) {
			$browser->loginAs($this->user)
				->visit(new Lesson())
				->waitFor('@side_nav', 15)
				->waitUntilMissing('@loading_overlay', 30)
				->switchToLessonFrame()
				->goThroughSlides()
				->assertLastSlide();
		});
	}

	public function tearDown()
	{
		parent::tearDown();
		$this->user->delete();
	}
}
