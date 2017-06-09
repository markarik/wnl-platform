<?php

namespace Tests\Browser;

use Tests\Browser\Pages\Course\Course;
use Tests\Browser\Pages\Course\Lesson;
use Tests\Browser\Pages\Login;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class FirstVisitTest extends DuskTestCase
{

	/**
	 * @dataProvider Tests\Browser\DataProviders\User::freshUserProvider
	 * @param String $email
	 * @param String $password
	 * @param String $name
	 */
	public function testFirstVisit($email, $password, $name)
	{
		$this->markTestSkipped('We need to figure out how to create fresh user every time or purge redis');
		$this->browse(function (Browser $browser) use ($email, $password, $name) {
			$browser->maximize()
				->visit(new Login())
				//TODO this is needed until we implement progress state in localStorage better
				->clearUserData()
				->loginAsUser($email, $password)
				->on(new Course())
				->waitTillLoaded()
				->with('@welcome_message_container', function ($welcomeMessageContainer) use ($name) {
					$welcomeMessageContainer->assertSee(sprintf('Witaj %s!', $name))
						->startFirstLesson();
				})
				->on(new Lesson())
				->switchToLessonFrame()
				->nextSlide()
				->assertNextSlide();
		});
	}
}
