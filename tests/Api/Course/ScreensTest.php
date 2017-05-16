<?php

namespace Tests\Api\Course;

use App\Models\Edition;
use App\Models\User;
use Tests\Api\ApiTestCase;

class ScreensTest extends ApiTestCase
{

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

}
