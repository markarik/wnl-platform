<?php

namespace Tests\Browser;

use App\Models\User;
use Tests\Browser\Pages\Course\Lesson;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class NavigateUsingSideNavTest extends DuskTestCase
{

	private $user;

	public function setUp(): void
	{
		parent::setUp();
		$this->user = factory(User::class)->create();
	}

	public function testNavigateUsingSideNav()
	{
		$this->browse(function (Browser $browser) {
			$browser->loginAs($this->user)
				->visit(new Lesson())
				->waitFor('@side_nav', 15)
				->goThroughSections()
				->assertSectionsVisited();
		});
	}

	public function tearDown()
	{
		parent::tearDown();
		$this->user->delete();
	}
}
