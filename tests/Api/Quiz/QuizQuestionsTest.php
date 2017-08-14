<?php

namespace Tests\Api\Quiz;

use App\Models\User;
use Tests\Api\ApiTestCase;


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
		$user = factory(User::class)->create();

		$data = [
			'fields'  => ['id', 'text', 'created_at'],
			'search'  => [
				'phrase' => 'Gastropareza',
				'mode'   => 'phrase_match',
			],
			'filters' => [
				[
					'tags' => ['LEK-2016'],
				],
				[
					'categories' => ['Kardiologia', 'Pulmonologia'],
				],
				[
					'query' => [
						'doesntHave' => ['quiz_set'],
					],
				],
				[
					'pre_filter' => [
						'name'   => 'quiz_by_correct_answer',
						'params' => ['user_id' => 255, 'correct' => false],
					],
				],
				[
					'pre_filter' => [
						'name'   => 'quiz_is_done',
						'params' => ['user_id' => 255, 'done' => false],
					],
				],
			],
//			'include' => 'comments,comments.profiles,reactions',
			'limit'   => 5,
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/quiz_questions/.filter'), $data);
dd($response->dump());
		$response
			->assertStatus(200);
	}
}
