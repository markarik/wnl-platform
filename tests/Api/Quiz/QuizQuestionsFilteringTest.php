<?php

namespace Tests\Api\Quiz;

use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Tests\Api\ApiTestCase;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Api\PrivateApi\QuizQuestionsApiController;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuizQuestionsFilteringTest extends ApiTestCase
{
	use DatabaseTransactions;

	/** @test * */
	public function activeFiltersNotUseSaved()
	{
		$user = factory(User::class)->create();

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/quiz_questions/.activeFilters'), [
				'useSavedFilters' => false
			]);

		$response
			->assertJson([]);
	}

	/** @test * */
	public function activeFiltersHasSaved()
	{
		$user = factory(User::class)->create();
		$redisKey = QuizQuestionsApiController::savedFiltersCacheKey('quiz_questions', $user->id);

		$mockedRedis = Redis::shouldReceive('get')
			->once()
			->with($redisKey)
			->andReturn(json_encode([['filter'], 'active-filter-path']));

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/quiz_questions/.activeFilters'), [
				'useSavedFilters' => true
			]);

		$response
			->assertSee('active-filter-path');

		$mockedRedis->verify();
	}

/** @test * */
	public function activeFiltersUseSaved()
	{
		$user = factory(User::class)->create();
		$redisKey = QuizQuestionsApiController::savedFiltersCacheKey('quiz_questions', $user->id);

		$mockedRedis = Redis::shouldReceive('get')
			->once()
			->with($redisKey)
			->andReturn(null);

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/quiz_questions/.activeFilters'), [
				'useSavedFilters' => true
			]);
		$response
			->assertJson([]);

		$mockedRedis->verify();
	}


	/** @test * */
	public function activeFiltersSave()
	{
		$user = factory(User::class)->create();

		UserSubscription::create([
			'user_id' => $user->id,
			'access_start' => Carbon::now()->subDays(1),
			'access_end' => Carbon::now()->addDays(1)
		]);

		$redisKey = QuizQuestionsApiController::savedFiltersCacheKey('quiz_questions', $user->id);

		$mockedRedis = Redis::shouldReceive('set')
			->once()
			->with($redisKey, json_encode([[], ['active-filter']]));

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/quiz_questions/.filter'), [
				'active' => ['active-filter'],
				'filters' => [],
				'saveFilters' => true
			]);

		$response
			->assertStatus(200);

		$mockedRedis->verify();
	}

	/** @test * */
	public function filtersPaginatedResponse()
	{
		$user = factory(User::class)->create();
		UserSubscription::create([
			'user_id' => $user->id,
			'access_start' => Carbon::now()->subDays(1),
			'access_end' => Carbon::now()->addDays(1)
		]);

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/quiz_questions/.filter'), [
				'filters' => []
			]);

		$response
			->assertJsonStructure(['total', 'has_more', 'last_page', 'per_page', 'current_page', 'data']);

		$response
			->assertJsonMissing(['from_cache', 'cache_hash']);
	}

	/** @test * */
	public function filtersCachedPaginated()
	{
		$user = factory(User::class)->create();
		UserSubscription::create([
			'user_id' => $user->id,
			'access_start' => Carbon::now()->subDays(1),
			'access_end' => Carbon::now()->addDays(1)
		]);

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/quiz_questions/.filter'), [
				'filters' => [[
					'quiz-resolution' => [
						'user_id' => 1,
						'date' => '2018-02-01',
						'list' =>  ['incorrect']
					]
				]],
				'active' => ['active-filter']
			]);

		$response
			->assertJsonStructure([
				'total',
				'has_more',
				'last_page',
				'per_page',
				'current_page',
				'data',
				'cache_hash'
			]);
	}
}
