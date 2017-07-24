<?php

namespace Tests\Api\Qna;

use App\Models\User;
use Tests\Api\ApiTestCase;


class EventsTest extends ApiTestCase
{

	/** @test */
	public function post_mention_from_chat_message()
	{
		$user = User::find(1);

		$data = [
			'subject' => [
				'type' => 'chat_message',
				'id'   => 1,
				'text' => 'Siema siema',
				'channel' => '#kardiologia-1'
			],
			'mentioned_users' => [2, 3],
		];

		$headers = [
			'X-BETHINK-LOCATION' => env('APP_URL') . '/app/courses/1/lessons/1/screens/1/1',
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/events/mentions'), $data, $headers);

		dump($response->dump());

		$response
			->assertStatus(200);
	}

	/** @test */
	public function post_mention_from_comment()
	{
		$user = User::find(1);

		$data = [
			'subject' => [
				'type' => 'comment',
				'id'   => 1,
			],
			'mentioned_users' => [2],
		];

		$headers = [
			'X-BETHINK-LOCATION' => env('APP_URL') . '/app/courses/1/lessons/1/screens/1/1',
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/events/mentions'), $data, $headers);

		dump($response->dump());

		$response
			->assertStatus(200);
	}
}
