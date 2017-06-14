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
				'lessons' => [
					'foo' => 'bar', 'fizz' => 'buzz'
				]
			]);

		$mockedRedis->verify();
	}

	/** @test */
	public function get_empty_course_state()
	{
		$user = User::find(1);
		$redisKey = UserStateApiController::getCourseRedisKey(1, 1);

		$mockedRedis = Redis::shouldReceive('get')
			->once()
			->with($redisKey)
			->andReturn(null);

		$response = $this
			->actingAs($user)
			->call('GET', $this->url("/users/{$user->id}/state/course/1"));

		$response
			->assertStatus(200)
			->assertJson([
				'lessons' => []
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
			->call('PUT', $this->url("/users/{$user->id}/state/course/1"), [
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

		$mockedRedis = Redis::shouldReceive('get')->once()->with($redisKey)->andReturn(json_encode(['foo' => 'bar']));

		$response = $this
			->actingAs($user)
			->call('GET', $this->url("/users/{$user->id}/state/course/1/lesson/1"));

		$response
			->assertStatus(200)
			->assertJson([
				'lesson' => [
					'foo' => 'bar'
				]
			]);

		$mockedRedis->verify();
	}

	/** @test */
	public function get_empty_lesson_state()
	{
		$user = User::find(1);
		$redisKey = UserStateApiController::getLessonRedisKey($user->id, 1, 1);

		$mockedRedis = Redis::shouldReceive('get')->once()->with($redisKey)->andReturn(null);

		$response = $this
			->actingAs($user)
			->call('GET', $this->url("/users/{$user->id}/state/course/1/lesson/1"));

		$response
			->assertStatus(200)
			->assertJson([
				'lesson' => []
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
			->call('PUT', $this->url("/users/{$user->id}/state/course/1/lesson/1"), [
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

	/** @test */
	public function get_quiz_state()
	{
		$user = User::find(1);
		$redisKey = UserStateApiController::getQuizRedisKey($user->id, 1);

		$mockedRedis = Redis::shouldReceive('get')->once()->with($redisKey)->andReturn(json_encode(['foo' => 'bar']));

		$response = $this
			->actingAs($user)
			->call('GET', $this->url("/users/{$user->id}/state/quiz/1"));

		$response
			->assertStatus(200)
			->assertJson([
				'quiz' => [
					'foo' => 'bar'
				]
			]);

		$mockedRedis->verify();
	}

	/** @test */
	public function get_empty_quiz_state()
	{
		$user = User::find(1);
		$redisKey = UserStateApiController::getQuizRedisKey($user->id, 1);

		$mockedRedis = Redis::shouldReceive('get')->once()->with($redisKey)->andReturn(null);

		$response = $this
			->actingAs($user)
			->call('GET', $this->url("/users/{$user->id}/state/quiz/1"));

		$response
			->assertStatus(200)
			->assertJson([
				'quiz' => []
			]);

		$mockedRedis->verify();
	}

	/** @test */
	public function update_quiz_state()
	{
		$user = User::find(1);
		$redisKey = UserStateApiController::getQuizRedisKey($user->id, 1);
		$encodedData = json_encode(['something']);

		$mockedRedis = Redis::shouldReceive('set')->once()->with($redisKey, $encodedData);

		$response = $this
			->actingAs($user)
			->call('PUT', $this->url("/users/{$user->id}/state/quiz/1"), [
				'quiz' => 'something'
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
