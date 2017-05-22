<?php

namespace Tests\Api\Comments;

use App\Models\User;
use Tests\Api\ApiTestCase;


class CommentsTest extends ApiTestCase
{

	/** @test */
	public function post_comment()
	{
		$user = User::find(1);

		$data = [
			'commentable_resource' => config('papi.resources.answers'),
			'commentable_id'       => 1,
			'text'                 => 'Kolekcjonuję antarktyczne drewniane kaczki, gdyby ktoś coś miał, proszę o info na priv. Pozdrawiam.',
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/comments'), $data);

		$response
			->assertStatus(200)
			->assertJsonStructure(['id', 'text', 'created_at', 'updated_at']);

	}

	/** @test */
	public function search_comments()
	{
		$user = User::find(1);

		$data = [
			'query' => [
				'whereHas' => [
					'quiz_questions' => [
						'where' => [
							['id', 'in', [1, 2, 3, 6, 10]],
						],
					],
				],
				'where'    => [
					['updated_at', '>', '1495033700'],
				],
			],
			'order' => [
				'created_at' => 'desc',
				'id'         => 'asc',
			],
			'limit' => [10, 0],
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/comments/.search'), $data);

		$response
			->assertStatus(200);
	}

	/** @test */
	public function delete_comment()
	{
		$user = User::find(4);

		$response = $this
			->actingAs($user)
			->json('DELETE', $this->url('/comments/2'));

		$response
			->assertStatus(200);
	}

}
