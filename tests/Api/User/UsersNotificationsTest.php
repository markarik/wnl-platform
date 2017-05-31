<?php

namespace Tests\Api\User;

use App\Models\User;
use Tests\Api\ApiTestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserNotificationsTest extends ApiTestCase
{
	/** @test */
	public function patch_notifications_mark_as_read()
	{
		$user = User::find(1);

		$payload = ['read_at' => time()];

		$response = $this
			->actingAs($user)
			->json('PATCH', $this->url("/users/{$user->id}/notifications"), $payload);

		$response
			->assertStatus(200);
	}
}
