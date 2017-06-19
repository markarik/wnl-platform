<?php

namespace Tests\Api\Quiz;

use App\Models\QuizQuestion;
use App\Models\QuizSet;
use App\Models\User;
use App\Models\UserQuizResults;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;


class QuizSetsTest extends ApiTestCase
{

	use DatabaseTransactions;

	/** @test * */
	public function get_quiz_set_stats()
	{
		$QUIZ_SET_ID = 500;

		factory(UserQuizResults::class)->create([
			'user_id' => 1,
			'quiz_question_id' => 1001,
			'quiz_answer_id' => 11
		]);

		factory(UserQuizResults::class)->create([
			'user_id' => 2,
			'quiz_question_id' => 1001,
			'quiz_answer_id' => 12
		]);

		factory(UserQuizResults::class)->create([
			'user_id' => 3,
			'quiz_question_id' => 1001,
			'quiz_answer_id' => 11
		]);

		factory(UserQuizResults::class)->create([
			'user_id' => 3,
			'quiz_question_id' => 1000,
			'quiz_answer_id' => 13
		]);

		$quizSet = factory(QuizSet::class)->create(['id' => $QUIZ_SET_ID]);

		$quizSet->questions()
			->saveMany([
				factory(QuizQuestion::class)->create(['id' => 1001]),
				factory(QuizQuestion::class)->create(['id' => 1000]),
			]);

		$user = User::find(1);
		$response = $this
			->actingAs($user)
			->json('GET', $this->url('/quiz_sets/'. $QUIZ_SET_ID . '/getStats'));

		$response
			->assertStatus(200)
			->assertJson([
				'stats' => [
					"11" => 2,
					"12" => 1,
					"13" => 1
				]
			]);
	}
}
