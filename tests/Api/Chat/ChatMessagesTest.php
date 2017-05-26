<?php


namespace Tests\Api\Chat;


use App\Models\User;
use Tests\Api\ApiTestCase;

class ChatMessagesTest extends ApiTestCase
{
	/** @test */
	public function get_public_chat_room_history()
	{
		$user = User::find(1);

		$data = [
			'query' => [
				'where' => [
					['created_at', '>', '1495033700'],
				],
			],
			'order' => [
				'created_at' => 'asc',
			],
			'limit' => [100, 0],
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/chat_rooms/courses-1/chat_messages/.search?include=profiles'), $data);

		$response
			->assertStatus(200);
	}
}