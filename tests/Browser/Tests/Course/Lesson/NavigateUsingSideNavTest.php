<?php

namespace Tests\Browser;

use App\Models\Order;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
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
		// TODO figure out how to make it cleaner
		UserSubscription::create([
			'user_id' => $this->user->id,
			'access_start' => Carbon::now(),
			'access_end' => Carbon::now()->addDays(20),
		]);
		Order::create([
			'user_id' => $this->user->id,
			'product_id' => 12,
			'paid' => true,
			'method' => 'online'
		]);
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
