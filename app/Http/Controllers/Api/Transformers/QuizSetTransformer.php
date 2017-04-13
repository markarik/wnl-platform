<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\QuizSet;
use League\Fractal\TransformerAbstract;

class QuizSetTransformer extends TransformerAbstract
{
	protected $availableIncludes = ['questions'];

	public function transform(QuizSet $quizSet)
	{
		return [
			'id'   => $quizSet->id,
			'name' => $quizSet->name,
		];
	}

	public function includeQuestions(QuizSet $quizSet)
	{
		$questions = $quizSet->questions;

		return $this->collection(
			$questions,
			new QuizQuestionTransformer([
				'quiz_sets' => $quizSet->id,
			]),
			'quiz_questions'
		);
	}
}
