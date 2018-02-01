<?php

namespace Tests\Api\Quiz;

use App\Models\User;
use Tests\Api\ApiTestCase;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Api\PrivateApi\QuizQuestionsApiController;

class QuizQuestionsFilteringTest extends ApiTestCase
{

	/** @test * */
	public function activeFiltersNotUseSaved()
	{
		$user = User::find(1);

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/quiz_questions/.activeFilters'), [
				'useSavedFilters' => false
			]);

		$response
			->assertJson([]);
	}

	/** @test * */
	public function activeFiltersUseSaved()
	{
		$user = User::find(1);
		$redisKey = QuizQuestionsApiController::savedFiltersCacheKey('quiz_questions', 1);

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
	public function activeFiltersHasSaved()
	{
		$user = User::find(1);
		$redisKey = QuizQuestionsApiController::savedFiltersCacheKey('quiz_questions', 1);

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
	public function activeFiltersSave()
	{
		$user = User::find(1);
		$redisKey = QuizQuestionsApiController::savedFiltersCacheKey('quiz_questions', 1);

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
		$user = User::find(1);

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
		$user = User::find(1);

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
