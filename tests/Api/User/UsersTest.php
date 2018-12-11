<?php

namespace Tests\Api\User;

use App\Models\User;
use Tests\Api\ApiTestCase;

class UsersTest extends ApiTestCase
{
	/** @test */
	public function regular_user_cant_access_other_users_info()
	{
		$user = factory(User::class)->create();

		$response = $this->actingAs($user)
			->json('GET', 'papi/v2/users/all');

		$response
			->assertStatus(403);
	}

	/** @test */
	public function regular_user_cant_filter_users()
	{
		$user = factory(User::class)->create();

		$response = $this->actingAs($user)
			->json('POST', 'papi/v2/users/.filter', []);

		$response
			->assertStatus(403);
	}
}
