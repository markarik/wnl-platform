<?php

namespace Tests\Api\User;

use App\Http\Controllers\Api\PrivateApi\User\UserStateApiController;
use App\Models\User;
use Illuminate\Support\Facades\Redis;
use Tests\Api\ApiTestCase;

class UserStateTest extends ApiTestCase
{
	/** @test */
	public function get_course_state()
	{
		$user = User::find(1);
		$redisKey = UserStateApiController::getCourseRedisKey(1, 1);

		$mockedRedis = Redis::shouldReceive('get')
			->once()
			->with($redisKey)
			->andReturn(json_encode([
				'foo' => 'bar', 'fizz' => 'buzz'
			]));

		$response = $this
			->actingAs($user)
			->call('GET', $this->url("/users/{$user->id}/state/course/1"));

		$response
			->assertStatus(200)
			->assertJson([
				'foo' => 'bar', 'fizz' => 'buzz'
			]);

		$mockedRedis->verify();
	}

	/** @test */
	public function update_course_state()
	{
		$user = User::find(1);
		$redisKey = UserStateApiController::getCourseRedisKey(1, 1);

		$encodedLessons = json_encode(['foo bar']);

		$mockedRedis = Redis::shouldReceive('set')->once()->with($redisKey, $encodedLessons);

		$response = $this
			->actingAs($user)
			->call('PATCH', $this->url("/users/{$user->id}/state/course/1"), [
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

	/** @test */
	public function get_lesson_state()
	{
		$user = User::find(1);
		$redisKey = UserStateApiController::getLessonRedisKey($user->id, 1, 1);

		$mockedRedis = Redis::shouldReceive('get')->once()->with($redisKey);

		$response = $this
			->actingAs($user)
			->call('GET', $this->url("/users/{$user->id}/state/course/1/lesson/1"), [
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

	/** @test */
	public function update_lesson_state()
	{
		$user = User::find(1);
		$redisKey = UserStateApiController::getLessonRedisKey($user->id, 1, 1);
		$encodedData = json_encode(['something']);

		$mockedRedis = Redis::shouldReceive('set')->once()->with($redisKey, $encodedData);

		$response = $this
			->actingAs($user)
			->call('PATCH', $this->url("/users/{$user->id}/state/course/1/lesson/1"), [
				'lesson' => 'something'
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
