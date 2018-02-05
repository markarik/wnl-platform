<?php


namespace Tests\Api\Chat;


use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;

class ChatMessagesTest extends ApiTestCase
{
	use DatabaseTransactions;

	/** @test */
	public function get_public_chat_room_history_for_non_existing_room()
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
			->json('POST', $this->url('/chat_rooms/not_existing_room/chat_messages/.search?include=profiles'), $data);

		$response
			->assertStatus(404);
	}

	/** @test */
	public function get_public_chat_room_history()
	{
		$user = User::find(1);
		$room = factory(ChatRoom::class)->create();

		$data = [
			'query'   => [
				'where' => [
					['time', '<', 1497612881],
				],
			],
			'order'   => [
				'time' => 'asc',
			],
			'limit'   => [10, 0],
			'include' => 'profiles',
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url("/chat_rooms/{$room->name}/chat_messages/.search"), $data);

		$response
			->assertStatus(200);
	}

	/** @test */
	public function get_private_chat_room_history()
	{
		$user = User::find(1);
		$room = factory(ChatRoom::class)->create([
			'name' => 'private-room'
		]);

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
			->json('POST', $this->url("/chat_rooms/{$room->name}/chat_messages/.search?include=profiles"), $data);

		$response
			->assertStatus(401);
	}
}
