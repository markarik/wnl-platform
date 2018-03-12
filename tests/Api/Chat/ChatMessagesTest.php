<?php


namespace Tests\Api\Chat;


use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;

class ChatMessagesTest extends ApiTestCase
{
	use DatabaseTransactions;

	/** @test */
	public function get_public_chat_room_history_nonexistent_room()
	{
		$user = factory(User::class)->create();

		$data = ['rooms' => [0]];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/chat_messages/.getByRooms'), $data);

		$response
			->assertStatus(404);
	}

	/** @test */
	public function get_public_chat_room_history()
	{
		$user = User::find(1);
		$room = factory(ChatRoom::class)->create(['type' => 'public']);
		$message = factory(ChatMessage::class)->create([
			'user_id'      => $user->id,
			'chat_room_id' => $room->id,
		]);

		$data = ['rooms' => [$room->id]];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url("/chat_messages/.getByRooms"), $data);

		$response
			->assertStatus(200)
			->assertJson([
				'data' => [[
					'content'      => $message->content,
					'id'           => $message->id,
					'time'         => $message->time,
					'chat_room_id' => $room->id,
				]],
				'cursor' => [
					'current' => null,
					'next' => $message->time,
					'previous' => null,
					'has_more' => true
				]
			]);
	}

	/** @test */
	public function get_private_chat_room_history_forbidden()
	{
		$users = factory(User::class, 3)->create();
		$room = factory(ChatRoom::class)->create([
			'type' => 'private',
		]);
		$room->users()->attach($users[0]);
		$room->users()->attach($users[1]);

		$data = [
			'rooms' => [$room->id],
		];

		$response = $this
			->actingAs($users[2])
			->json('POST', $this->url("/chat_messages/.getByRooms"), $data);

		$response
			->assertStatus(403);
	}

	/** @test */
	public function get_public_chat_room_history_forbidden()
	{
		$user = factory(User::class)->create();
		$room = factory(ChatRoom::class)->create([
			'type' => 'public',
		]);

		$roleAccess = Permission::slug('role_access');
		$moderatorsRole = Role::byName('moderator');
		$roleAccess->chatRooms()->syncWithoutDetaching($room);
		$room->roles()->syncWithoutDetaching($moderatorsRole);

		$data = [
			'rooms' => [$room->id],
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url("/chat_messages/.getByRooms"), $data);

		$response
			->assertStatus(403);
	}
}
