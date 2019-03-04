<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\QuizSet;
use App\Http\Controllers\Api\ApiTransformer;

class QuizSetTransformer extends ApiTransformer
{
	protected $availableIncludes = ['questions'];

	public function transform(QuizSet $quizSet)
	{
		return [
			'id'   => $quizSet->id,
			'name' => $quizSet->name,
			'description' => $quizSet->description,
			'lesson_id' => $quizSet->lesson_id,
		];
	}

	public function includeQuestions(QuizSet $quizSet)
	{
		$questions = $quizSet->questions()->get();

		return $this->collection(
			$questions,
			new QuizQuestionTransformer([
				'quiz_sets' => $quizSet->id,
			])
		);
	}
}
