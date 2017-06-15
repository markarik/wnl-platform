<?php

namespace Tests\Api\Qna;

use App\Models\User;
use Tests\Api\ApiTestCase;


class QuestionsTest extends ApiTestCase
{

	/** @test */
	public function post_qna_question()
	{
		$user = User::find(1);

		$data = [
			'text' => 'Meine Damen und Herren, hertzlich willkommen und nicht verstehen!',
			'tags' => [4, 5, 6],
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/qna_questions'), $data);

		$response
			->assertStatus(200);
	}

	/** @test * */
	public function search_qna_questions()
	{
		$user = User::find(1);

		$data = [
			'query' => [
				'hasIn' => [
					'tags' => ['tags.id', [5, 6]],
				],
			],
		];
		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/qna_questions/.search'), $data);

		$response
			->assertStatus(200);
	}
}
