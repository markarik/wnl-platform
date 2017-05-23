<?php

namespace Tests\Api\Course;

use App\Models\Edition;
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

		$payload = ['order_number' => 5];

		$response = $this
			->actingAs($user)
			->json('PATCH', $this->url('/screens/3'), $payload);

		$response
			->assertStatus(200);
	}

	/** @test */
	public function delete_screen_acting_as_regular_user()
	{
		$user = User::find(4);

		$response = $this
			->actingAs($user)
			->json('DELETE', $this->url('/screens/3'));

		$response
			->assertStatus(401);
	}

	/** @test */
	public function delete_screen_acting_as_admin()
	{
		$user = User::find(1);

		$response = $this
			->actingAs($user)
			->json('DELETE', $this->url('/screens/3'));

		$response
			->assertStatus(200);
	}

}
