<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\QuizSet;
use App\Http\Controllers\Api\ApiTransformer;

class QuizSetTransformer extends ApiTransformer
{
	protected $availableIncludes = ['quiz_questions'];

	public function transform(QuizSet $quizSet)
	{
		return [
			'id'   => $quizSet->id,
			'name' => $quizSet->name,
			'description' => $quizSet->description,
			'lesson_id' => $quizSet->lesson_id,
		];
	}

	public function includeQuizQuestions(QuizSet $quizSet)
	{
		$questions = $quizSet->questions()->orderBy('order_number')->get();

		return $this->collection(
			$questions,
			new QuizQuestionTransformer([
				'quiz_sets' => $quizSet->id,
			])
		);
	}
}
