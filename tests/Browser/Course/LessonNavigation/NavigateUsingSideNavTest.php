<?php

namespace Tests\Browser;

use Tests\Browser\Pages\Course\Lesson;
use Tests\Browser\Pages\Login;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class NavigateUsingSideNavTest extends DuskTestCase
{

	public function testNavigateUsingSideNav()
	{
		$this->browse(function (Browser $browser) {
			$browser->maximize()
				->visit(new Login())
				->loginAsUser('jlkarminski@gmail.com', 'secret')
				->visit('/app/courses/1/lessons/1/screens/1')
				->on(new Lesson())
				->waitFor('@side_nav', 15)
				->goThroughSections()
				->assertSectionsVisited();
		});
	}
}
