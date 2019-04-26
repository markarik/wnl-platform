<?php

namespace Tests\Api\Qna;

use App\Models\QnaAnswer;
use App\Models\QnaQuestion;
use App\Models\Role;
use App\Models\User;
use Mail;
use Tests\Api\ApiTestCase;


class AnswerTest extends ApiTestCase
{

	/** @test */
	public function post_qna_answer()
	{
		QnaAnswer::flushEventListeners();
		QnaQuestion::flushEventListeners();
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
		QnaQuestion::flushEventListeners();
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

	public function testQnaAnswerVerifyUnverify()
	{
		QnaAnswer::flushEventListeners();
		QnaQuestion::flushEventListeners();

		/** @var User $user */
		$user = factory(User::class)->create();
		$moderatorRole = Role::byName('moderator');
		$user->roles()->attach($moderatorRole);

		$qnaAnswer = factory(QnaAnswer::class)->create();

		$response = $this
			->actingAs($user)
			->json('GET', $this->url("/qna_answers/{$qnaAnswer->id}"));
		$response->assertStatus(200);
		$this->assertEquals(null, $response->json()['verified_at']);

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url("/qna_answers/{$qnaAnswer->id}"), [ 'verified' => true ]);
		$response->assertStatus(200);
		$this->assertNotEquals(null, $response->json()['verified_at']);

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url("/qna_answers/{$qnaAnswer->id}"), [ 'verified' => false ]);
		$response->assertStatus(200);
		$this->assertEquals(null, $response->json()['verified_at']);
	}
}
