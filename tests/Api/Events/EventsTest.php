<?php

namespace Tests\Api\Qna;

use App\Models\User;
use Tests\Api\ApiTestCase;


class EventsTest extends ApiTestCase
{

	/** @test */
	public function post_mentioned_event()
	{
		$user = User::find(1);

		$data = [
			'origin_id'       => 1,
			'origin_resource' => 'comments',
			'mentioned_users' => [1, 2, 3],
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/events/mentions'), $data);
		dd($response->dump());
		$response
			->assertStatus(200);
	}
}
