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
			'mentions'        => [1, 2, 3],
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/events/mentions'), $data);

		$response
			->assertStatus(200);
	}
}
