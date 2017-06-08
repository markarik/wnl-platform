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

		$mockedRedis = Redis::shouldReceive('hmget')
			->once()
			->with('user-state-1-1', ['lessonId', 'status', 'route', 'courseId'])
			->andReturn([
				'foo', 'bar', 'fizz', 'buzz'
			]);

		$response = $this
			->actingAs($user)
			->call('GET', $this->url("/users/{$user->id}/state"));

		$response
			->assertStatus(200)
			->assertJson([
				'lessonId' => 'foo',
				'status' => 'bar',
				'route' => 'fizz',
				'courseId' => 'buzz'
			]);

		$mockedRedis->verify();
	}

	/** @test */
	public function update_state()
	{
		$user = User::find(1);

		$mockedRedis = Redis::shouldReceive('hmset')->once()->with('user-state-1-1', [
			'lessonId' => 1,
			'courseId' => 1,
			'route' => json_encode([]),
			'status' => "in-progress"
		]);

		$response = $this
			->actingAs($user)
			->json('PATCH', $this->url("/users/{$user->id}/state"), [
				'lessonId' => 1,
				'courseId' => 1,
				'route' => [],
				'status' => "in-progress"
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
