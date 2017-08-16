<?php

namespace Tests\Api\Qna;

use App\Models\Comment;
use App\Models\User;
use Tests\Api\ApiTestCase;


class EventsTest extends ApiTestCase
{

	/** @test */
	public function post_mention_from_chat_message()
	{
		$user = factory(User::class)->create();
		$mentioned = factory(User::class)->create();

		$data = [
			'subject' => [
				'type' => 'chat_message',
				'id'   => 1,
				'text' => 'Siema siema',
				'channel' => '#kardiologia-1'
			],
			'mentioned_users' => [$mentioned->id],
		];

		$headers = [
			'X-BETHINK-LOCATION' => env('APP_URL') . '/app/courses/1/lessons/1/screens/1/1',
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/events/mentions'), $data, $headers);

		$response
			->assertStatus(200);
	}

	/** @test */
	public function post_mention_from_comment()
	{
		$user = User::find(1);
		$mentioned = factory(User::class)->create();
		$comment = factory(Comment::class)->create();

		$data = [
			'subject' => [
				'type' => 'comment',
				'id'   => $comment->id,
			],
			'mentioned_users' => [$mentioned->id],
		];

		$headers = [
			'X-BETHINK-LOCATION' => env('APP_URL') . '/app/courses/1/lessons/1/screens/1/1',
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/events/mentions'), $data, $headers);

		$response
			->assertStatus(200);
	}
}
