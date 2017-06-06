<?php

namespace Tests\Browser;

use Tests\Browser\Pages\Course\Lesson;
use Tests\Browser\Pages\Login;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class NavigateUsingSideNavTest extends DuskTestCase
{

	/**
	 * @dataProvider Tests\Browser\DataProviders\User::userProvider
	 * @param String $email
	 * @param String $password
	 */
	public function testNavigateUsingSideNav($email, $password)
	{
		$this->browse(function (Browser $browser) use ($email, $password) {
			$browser->maximize()
				->visit(new Login())
				->loginAsUser($email, $password)
				->visit(new Lesson())
				->waitFor('@side_nav', 15)
				->goThroughSections()
				->assertSectionsVisited();
		});
	}
}
