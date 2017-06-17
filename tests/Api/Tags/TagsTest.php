<?php

namespace Tests\Api\Quiz;

use App\Models\User;
use Tests\Api\ApiTestCase;


class QuizQuestionsTest extends ApiTestCase
{

	/** @test * */
	public function search_tags()
	{
		$user = User::find(1);

		$data = [
			'query' => [
				'whereIn' => ['name', [1, 2, 3]],
			],
		];
		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/tags/.search'), $data);

		$response
			->assertStatus(200);
	}
}
