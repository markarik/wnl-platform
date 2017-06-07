<?php

namespace Tests\Api\User;

use App\Models\User;
use Tests\Api\ApiTestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserStateTest extends ApiTestCase
{
	/** @test */
	public function get_state()
	{
		$user = User::find(1);

		$response = $this
			->actingAs($user)
			->json('GET', $this->url("/users/{$user->id}/state"), []);

		$response
			->assertStatus(200)
			->assertJson([
				'status' => 'all good'
			]);
	}

	/** @test */
	public function update_state()
	{
		$user = User::find(1);

		$response = $this
			->actingAs($user)
			->json('PATCH', $this->url("/users/{$user->id}/state"), []);

		$response
			->assertStatus(200)
			->assertJson([
				'message' => 'OK',
				'status_code' => 200
			]);
	}
}
