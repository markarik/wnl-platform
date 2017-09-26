<?php

namespace Tests\Api\Quiz;

use App\Models\User;
use Tests\Api\ApiTestCase;
use Carbon\Carbon;


class QuizQuestionsTest extends ApiTestCase
{

	/** @test * */
	public function search_quiz_questions()
	{
		$user = User::find(1);

		$data = [
			'query' => [
				'whereIn' => ['id', [1, 2, 3]],
			],
		];
		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/quiz_questions/.search'), $data);

		$response
			->assertStatus(200);
	}

	/** @test * */
	public function search_quiz_questions_by_tag_name()
	{
		$user = User::find(1);

		$data = [
			'query' => [
				'whereHas' => [
					'tags' => [
						'where' => [
							['tags.name', '=', 'Kardiologia 1'],
						],
					],
				],
			],
		];
		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/quiz_questions/.search'), $data);

		$response
			->assertStatus(200);
	}

	/** @test */
	public function filter_quiz_questions()
	{
		$user = User::find(1);

		$data = [
			// 'fields'  => ['id', 'text', 'created_at'],
			'filters' => [
//				[
//					'search' => [
//						'phrase' => 'Gastropareza',
//						'mode'   => 'phrase_match',
//					],
//				],
				[
					'tags' => ['łatwe'],
				],
				[
					'query' => [
						'doesntHave' => 'sets',
					],
				],
				[
					'quiz-resolution' => [
						'user_id' => 255,
						'list'    => ['correct', 'incorrect', 'unresolved'],
					],
				],
				[
					'quiz-planned' => [
						'user_id' => 2,
						'list'    => ['all'],
					],
				],

			],
//			'include' => 'comments,comments.profiles,reactions',
			'limit'   => 5,
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/quiz_questions/.filter'), $data);

		$response
			->assertStatus(200);
	}

	/** @test */
	public function get_quiz_questions_filters_no_active_filters()
	{
		$this->markTestSkipped();
		$user = factory(User::class)->create();

		$data = [
			'filters' => [],
			'limit'   => 1,
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/quiz_questions/.filterList'), $data);

		$response
			->assertStatus(200);
	}

	/** @test */
	public function get_quiz_questions_filters_with_active_filters()
	{
		$this->markTestSkipped();
		$user = User::find(1);

		$data = [
			'filters' => [
//				[
//					'by_taxonomy-exams' => [119],
//				],
//				[
//					'quiz-resolution' => [
//						'user_id' => 1,
//						'list'    => ['correct'],
//					],
//				],
				[
					'quiz-planned' => [
						'user_id' => 2,
						'list'    => [Carbon::now()->toDateTimeString()],
					],
				],
			],
			'limit'   => 1,
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/quiz_questions/.filterList'), $data);

		$response
			->assertStatus(200);
	}
}
