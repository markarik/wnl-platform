<?php

namespace Tests\Api\User;

use App\Models\User;
use Tests\Api\ApiTestCase;

class UserProfilesTest extends ApiTestCase
{
	/** @test */
	public function search_user_profiles()
	{
		$user = User::find(1);

		$data = [
			'query' => [
				'where' => [
					['first_name', 'like', 'Adam'],
					['last_name', 'like', 'Karmiński'],
				],
			],
			'limit' => [5, 0],
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/user_profiles/.search'), $data);

		$response
			->assertStatus(200);
	}
}
