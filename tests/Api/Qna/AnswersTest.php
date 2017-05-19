<?php

namespace Tests\Api\Qna;

use App\Models\User;
use Tests\Api\ApiTestCase;


class AnswerTest extends ApiTestCase
{

	/** @test */
	public function post_qna_answer()
	{
		$user = User::find(1);

		$data = [
			'text'        => 'Jawohl!',
			'question_id' => 4,
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/qna_answers'), $data);

		$response
			->assertStatus(200);
	}

}
