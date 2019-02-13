<?php

namespace Tests\Api\Qna;

use App\Models\QnaAnswer;
use App\Models\QnaQuestion;
use App\Models\User;
use Mail;
use Tests\Api\ApiTestCase;


class AnswerTest extends ApiTestCase
{

	/** @test */
	public function post_qna_answer()
	{
		QnaAnswer::flushEventListeners();
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
	public function update_answer()
	{
		QnaAnswer::flushEventListeners();
		$user = User::find(1);
		$qnaAnswer = factory(QnaAnswer::class)->create();

		$data = [
			'text' => 'joł!',
		];

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url("/qna_answers/{$qnaAnswer->id}"), $data);

		$response
			->assertStatus(200);
	}
}
