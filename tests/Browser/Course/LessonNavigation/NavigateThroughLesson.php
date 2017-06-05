<?php

namespace Tests\Browser;

use Tests\Browser\Pages\Course\Course;
use Tests\Browser\Pages\Course\Lesson;
use Tests\Browser\Pages\Login;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class NavigateThroughLesson extends DuskTestCase
{

	public function testNavigateThroughLesson()
	{
		$this->browse(function (Browser $browser) {
			$browser->maximize()
				->visit(new Login())
				->loginAsUser('jlkarminski@gmail.com', 'secret')
				->on(new Course())
				->waitTillLoaded()
				->visit('/app/courses/1/lessons/1/screens/1')
				->on(new Lesson())
				->switchToLessonFrame()
				->goThroughSlides()
				->assertLastSlide();
		});
	}
}
