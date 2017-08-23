<?php

namespace Tests\Api\Quiz;

use App\Models\QuizQuestion;
use App\Models\QuizSet;
use App\Models\User;
use App\Models\UserQuizResults;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;


class QuizStatsTest extends ApiTestCase
{

	use DatabaseTransactions;

	/** @test * */
	public function get_quiz_set_stats()
	{
		$quizSet = factory(QuizSet::class)->create();

		$quizSet->questions()
			->saveMany([
				$q1 = factory(QuizQuestion::class)->create(),
				$q2 = factory(QuizQuestion::class)->create(),
			]);

		factory(UserQuizResults::class)->create([
			'user_id' => 1,
			'quiz_question_id' => $q1->id,
			'quiz_answer_id' => 11
		]);

		factory(UserQuizResults::class)->create([
			'user_id' => 2,
			'quiz_question_id' => $q1->id,
			'quiz_answer_id' => 12
		]);

		factory(UserQuizResults::class)->create([
			'user_id' => 3,
			'quiz_question_id' => $q1->id,
			'quiz_answer_id' => 11
		]);

		factory(UserQuizResults::class)->create([
			'user_id' => 3,
			'quiz_question_id' => $q2->id,
			'quiz_answer_id' => 13
		]);

		$user = User::find(1);
		$response = $this
			->actingAs($user)
			->json('GET', $this->url('/quiz_sets/'. $quizSet->id . '/stats'));

		$response
			->assertStatus(200)
			->assertJson([
				'stats' => [
					$q1->id => [
						"11" => 2,
						"12" => 1
					],
					$q2->id => [
						"13" => 1
					]
				]
			]);
	}
}
