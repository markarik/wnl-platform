<?php

namespace Tests\Browser;

use Tests\Browser\Pages\Course\Course;
use Tests\Browser\Pages\Course\Lesson;
use Tests\Browser\Pages\Login;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class SlidesNavigation extends DuskTestCase
{

	public function testFirstVisit()
	{
		$this->browse(function (Browser $browser) {
			$browser->maximize()
				->visit(new Login())
				->loginAsUser('jlkarminski@gmail.com', 'secret')
				->on(new Course())
				->waitTillLoaded()
				->with('@welcome_message_container', function ($welcomeMessageContainer) {
					$welcomeMessageContainer->assertSee('Witaj Kuba!')
						->startFirstLesson();
				})
				->on(new Lesson())
				->switchToLessonFrame()
				->nextSlide()
				->assertNextSlide();
		});
	}
}
