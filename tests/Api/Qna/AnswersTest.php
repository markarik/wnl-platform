<?php

namespace Tests\Api\Qna;

use App\Models\QnaQuestion;
use App\Models\User;
use Tests\Api\ApiTestCase;


class AnswerTest extends ApiTestCase
{

	/** @test */
	public function post_qna_answer()
	{
		$user = User::find(1);
		$question = factory(QnaQuestion::class)->create();

		$data = [
			'text'        => 'Jawohl!',
			'question_id' => $question->id,
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/qna_answers'), $data);

		$response
			->assertStatus(200);
	}

	/** @test */
	public function search_latest_answer()
	{
		$user = User::find(1);

		$data = [
			'query' => [
				'where' => [
					['question_id', '=', 1],
				],
			],
			'order' => [
				'created_at' => 'desc',
			],
			'limit' => [1, 0],
			'include' => 'profiles,comments',
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/qna_answers/.search'), $data);

		$response
			->assertStatus(200);
	}

	/** @test */
	public function update_answer()
	{
		$user = User::find(1);

		$data = [
			'text' => 'joł!',
		];

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url('/qna_answers/1'), $data);

		$response
			->assertStatus(200);
	}
}
