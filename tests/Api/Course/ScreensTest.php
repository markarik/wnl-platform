<?php

namespace Tests\Api\Course;

use App\Models\Screen;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;

class ScreensTest extends ApiTestCase
{
	use DatabaseTransactions;

	/** @test */
	public function patch_screen()
	{
		$user = User::find(1);
		$screen = factory(Screen::class)->create();

		$payload = ['order_number' => 5];

		$response = $this
			->actingAs($user)
			->json('PATCH', $this->url("/screens/{$screen->id}"), $payload);

		$response
			->assertStatus(200);
	}

	/** @test */
	public function delete_screen_acting_as_regular_user()
	{
		$user = factory(User::class)->create();
		$screen = factory(Screen::class)->create();

		$response = $this
			->actingAs($user)
			->json('DELETE', $this->url("/screens/{$screen->id}"));

		$response
			->assertStatus(403);
	}

	/** @test */
	public function delete_screen_acting_as_admin()
	{
		$user = User::find(1);
		$screen = factory(Screen::class)->create();

		$response = $this
			->actingAs($user)
			->json('DELETE', $this->url("/screens/{$screen->id}"));

		$response
			->assertStatus(200);
	}

}
