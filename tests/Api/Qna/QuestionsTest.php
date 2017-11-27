<?php

namespace Tests\Api\Qna;

use App\Models\Tag;
use App\Models\User;
use Tests\Api\ApiTestCase;


class QuestionsTest extends ApiTestCase
{

	/** @test */
	public function post_qna_question()
	{
		$user = User::find(1);
		$tags = collect(factory(Tag::class, 3)->create())->pluck('id');

		$data = [
			'text' => 'Meine Damen und Herren, hertzlich willkommen und nicht verstehen!',
			'tags' => $tags,
			'context' => ['foo' => 'bar']
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

	/** @test * */
	public function search_qna_questions_by_tag_name()
	{
		$user = User::find(1);

		$data = [
			'query' => [
				'whereHas' => [
					'tags' => [
						'where' => [
							['tags.name', '=', 'Kardiologia 1']
						],
					],
				],
				'whereIn' => ['id', [1, 2, 3]],
			],
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/qna_questions/.search'), $data);

		$response
			->assertStatus(200);
	}
}
