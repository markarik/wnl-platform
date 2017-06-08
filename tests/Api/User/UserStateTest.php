<?php

namespace Tests\Api\User;

use App\Models\User;
use Illuminate\Support\Facades\Redis;
use Tests\Api\ApiTestCase;

class UserStateTest extends ApiTestCase
{
	/** @test */
	public function get_state()
	{
		$user = User::find(1);

		$mockedRedis = Redis::shouldReceive('get')
			->once()
			->with('user-state-1-1')
			->andReturn(json_encode([
				'foo' => 'bar', 'fizz' => 'buzz'
			]));

		$response = $this
			->actingAs($user)
			->call('GET', $this->url("/users/{$user->id}/state"));

		$response
			->assertStatus(200)
			->assertJson([
				'foo' => 'bar', 'fizz' => 'buzz'
			]);

		$mockedRedis->verify();
	}

	/** @test */
	public function update_state()
	{
		$user = User::find(1);

		$encodedLessons = json_encode(['foo bar']);

		$mockedRedis = Redis::shouldReceive('set')->once()->with('user-state-1-1', $encodedLessons);

		$response = $this
			->actingAs($user)
			->call('PATCH', $this->url("/users/{$user->id}/state"), [
				'lessons' => 'foo bar'
			]);

		$response
			->assertStatus(200)
			->assertJson([
				'message' => 'OK',
				'status_code' => 200
			]);

		$mockedRedis->verify();
	}
}
