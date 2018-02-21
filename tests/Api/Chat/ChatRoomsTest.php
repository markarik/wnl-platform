<?php


namespace Tests\Api\Chat;


use App\Models\ChatRoom;
use App\Models\ChatRoomUser;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;

class ChatRoomsTest extends ApiTestCase
{
	use DatabaseTransactions;

	/** @test */
	public function create_private_room_when_not_exists()
	{
        $userOne = factory(User::class)->create();
        $userTwo = factory(User::class)->create();

        $chatRoomName = "private-{$userOne->id}-{$userTwo->id}";

		$data = [
            'name' => $chatRoomName,
            'users' => [$userOne->id, $userTwo->id]
		];

		$response = $this
			->actingAs($userOne)
			->json('POST', $this->url('/chat_rooms/.createPrivateRoom'), $data);

		$response
            ->assertStatus(200);

        $responseJson = $response->json();

        $this->assertDatabaseHas('chat_rooms', [
           'name' => $chatRoomName,
           'id' => $responseJson['id']
        ]);

        $this->assertDatabaseHas('chat_room_user', [
            'chat_room_id' => $responseJson['id'],
            'user_id' => $userOne->id
         ]);

         $this->assertDatabaseHas('chat_room_user', [
            'chat_room_id' => $responseJson['id'],
            'user_id' => $userTwo->id
         ]);
    }

    /** @test */
	public function create_private_room_when_similar_exisits()
	{
        $userOne = factory(User::class)->create();
        $userTwo = factory(User::class)->create();
        $userThree = factory(User::class)->create();

        $chatRoom = factory(ChatRoom::class)->create([
            'name' => "private-{$userOne->id}-{$userTwo->id}"
        ]);

        $chatRoomName = "private-{$userOne->id}-{$userThree->id}";

		$data = [
            'name' => $chatRoomName,
            'users' => [$userOne->id, $userThree->id]
		];

		$response = $this
			->actingAs($userOne)
			->json('POST', $this->url('/chat_rooms/.createPrivateRoom'), $data);

		$response
            ->assertStatus(200);

        $responseJson = $response->json();

        $this->assertNotEquals($chatRoom->id, $responseJson['id']);

        $this->assertDatabaseHas('chat_rooms', [
           'name' => $chatRoomName,
           'id' => $responseJson['id']
        ]);

        $this->assertDatabaseHas('chat_room_user', [
            'chat_room_id' => $responseJson['id'],
            'user_id' => $userOne->id
         ]);

         $this->assertDatabaseHas('chat_room_user', [
            'chat_room_id' => $responseJson['id'],
            'user_id' => $userThree->id
         ]);
    }

    /** @test */
	public function create_private_room_when_exists()
	{
        $userOne = factory(User::class)->create();
        $userTwo = factory(User::class)->create();
        $chatRoomName = "private-{$userOne->id}-{$userTwo->id}";

        $chatRoom = factory(ChatRoom::class)->create([
            'name' => $chatRoomName
        ]);

        $chatRoom->users()->attach($userOne);
        $chatRoom->users()->attach($userTwo);

        $usersInRoom = ChatRoomUser::where('chat_room_id', $chatRoom->id)->count();
        $this->assertEquals(2, $usersInRoom);

		$data = [
            'name' => $chatRoomName,
            'users' => [$userOne->id, $userTwo->id]
		];

		$response = $this
			->actingAs($userOne)
			->json('POST', $this->url('/chat_rooms/.createPrivateRoom'), $data);

		$response
            ->assertStatus(200);

        $responseJson = $response->json();

        $afterRequestUsersInRoom = ChatRoomUser::where('chat_room_id', $chatRoom->id)->count();

        $this->assertEquals($afterRequestUsersInRoom, 2);
        $this->assertEquals($responseJson['id'], $chatRoom->id);

        $this->assertDatabaseHas('chat_rooms', [
           'name' => $chatRoomName,
           'id' => $responseJson['id']
        ]);

        $this->assertDatabaseHas('chat_room_user', [
            'chat_room_id' => $responseJson['id'],
            'user_id' => $userOne->id
         ]);

         $this->assertDatabaseHas('chat_room_user', [
            'chat_room_id' => $responseJson['id'],
            'user_id' => $userTwo->id
         ]);
    }

    /** @test */
    public function create_private_room_when_exists_different_order()
	{
        $userOne = factory(User::class)->create();
        $userTwo = factory(User::class)->create();
        $chatRoomName = "private-{$userTwo->id}-{$userOne->id}";

        $chatRoom = factory(ChatRoom::class)->create([
            'name' => $chatRoomName
        ]);

        $chatRoom->users()->attach($userOne);
        $chatRoom->users()->attach($userTwo);

        $usersInRoom = ChatRoomUser::where('chat_room_id', $chatRoom->id)->count();
        $this->assertEquals(2, $usersInRoom);

		$data = [
            'name' => $chatRoomName,
            'users' => [$userOne->id, $userTwo->id]
		];

		$response = $this
			->actingAs($userOne)
			->json('POST', $this->url('/chat_rooms/.createPrivateRoom'), $data);

		$response
            ->assertStatus(200);

        $responseJson = $response->json();

        $afterRequestUsersInRoom = ChatRoomUser::where('chat_room_id', $chatRoom->id)->count();

        $this->assertEquals($afterRequestUsersInRoom, 2);
        $this->assertEquals($responseJson['id'], $chatRoom->id);

        $this->assertDatabaseHas('chat_rooms', [
           'name' => $chatRoomName,
           'id' => $responseJson['id']
        ]);

        $this->assertDatabaseMissing('chat_rooms', [
            'name' => "private-{$userOne->id}-{$userTwo->id}",
         ]);

        $this->assertDatabaseHas('chat_room_user', [
            'chat_room_id' => $responseJson['id'],
            'user_id' => $userOne->id
         ]);

         $this->assertDatabaseHas('chat_room_user', [
            'chat_room_id' => $responseJson['id'],
            'user_id' => $userTwo->id
         ]);
    }

    /** @test */
    public function create_private_room_with_subset_of_users()
	{
        $userOne = factory(User::class)->create();
        $userTwo = factory(User::class)->create();
        $userThree = factory(User::class)->create();
        $chatRoomName = "private-{$userOne->id}-{$userTwo->id}-{$userThree->id}";

        $chatRoom = factory(ChatRoom::class)->create([
            'name' => $chatRoomName
        ]);

        $chatRoom->users()->attach($userOne);
        $chatRoom->users()->attach($userTwo);
        $chatRoom->users()->attach($userThree);

        $usersInRoom = ChatRoomUser::where('chat_room_id', $chatRoom->id)->count();
        $this->assertEquals(3, $usersInRoom);

		$data = [
            'name' => "private-{$userOne->id}-{$userTwo->id}",
            'users' => [$userOne->id, $userTwo->id]
		];

		$response = $this
			->actingAs($userOne)
			->json('POST', $this->url('/chat_rooms/.createPrivateRoom'), $data);

		$response
            ->assertStatus(200);

        $responseJson = $response->json();

        $afterRequestUsersInRoom = ChatRoomUser::where('chat_room_id', $responseJson['id'])->count();

        $this->assertEquals(2, $afterRequestUsersInRoom);
        $this->assertNotEquals($responseJson['id'], $chatRoom->id);

        $this->assertDatabaseHas('chat_rooms', [
           'name' => $data['name'],
           'id' => $responseJson['id']
        ]);

        $this->assertDatabaseHas('chat_room_user', [
            'chat_room_id' => $responseJson['id'],
            'user_id' => $userOne->id
         ]);

         $this->assertDatabaseHas('chat_room_user', [
            'chat_room_id' => $responseJson['id'],
            'user_id' => $userTwo->id
         ]);

         $this->assertDatabaseMissing('chat_room_user', [
            'chat_room_id' => $responseJson['id'],
            'user_id' => $userThree->id
         ]);
    }

    /** @test */
    public function create_room_with_myself()
	{
        $userOne = factory(User::class)->create();
        $userTwo = factory(User::class)->create();
        $chatRoomName = "private-{$userOne->id}";

        $chatRoom = factory(ChatRoom::class)->create([
            'name' => "private-{$userOne->id}-{$userTwo->id}"
        ]);

        $chatRoom->users()->attach($userOne);
        $chatRoom->users()->attach($userTwo);

        $usersInRoom = ChatRoomUser::where('chat_room_id', $chatRoom->id)->count();
        $this->assertEquals(2, $usersInRoom);

		$data = [
            'name' => "private-{$userOne->id}",
            'users' => [$userOne->id]
		];

		$response = $this
			->actingAs($userOne)
			->json('POST', $this->url('/chat_rooms/.createPrivateRoom'), $data);

		$response
            ->assertStatus(200);

        $responseJson = $response->json();
        $afterRequestUsersInRoom = ChatRoomUser::where('chat_room_id', $responseJson['id'])->count();

        $this->assertEquals(1, $afterRequestUsersInRoom);
        $this->assertNotEquals($responseJson['id'], $chatRoom->id);

        $this->assertDatabaseHas('chat_rooms', [
           'name' => $data['name'],
           'id' => $responseJson['id']
        ]);

        $this->assertDatabaseHas('chat_room_user', [
            'chat_room_id' => $responseJson['id'],
            'user_id' => $userOne->id
         ]);

         $this->assertDatabaseMissing('chat_room_user', [
            'chat_room_id' => $responseJson['id'],
            'user_id' => $userTwo->id
         ]);
    }
}
