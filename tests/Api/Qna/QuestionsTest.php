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
			'text'      => 'Meine Damen und Herren, hertzlich willkommen und nicht verstehen!',
			'lesson_id' => 1,
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/qna_questions'), $data);

		$response
			->assertStatus(200);
	}

	/** @test **/
	public function search_qna_questions()
	{
		$user = User::find(1);

		$data = [
			'query' => [
				'whereHas' => [
					'tags' => [
						'where' => [
							['name', 'in', ['interna', 'pulmonologia']],
						],
					],
				],
			],
			'order' => [
				'created_at' => 'desc',
				'id'         => 'asc',
			],
			'limit' => [10, 0],
			'include'=> '',
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/qna_questions/.search'), $data);

		$response
			->assertStatus(200);
	}
}
