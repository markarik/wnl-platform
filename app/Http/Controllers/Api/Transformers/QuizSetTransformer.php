<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\QuizSet;
use League\Fractal\TransformerAbstract;

class QuizSetTransformer extends TransformerAbstract
{
	protected $availableIncludes = ['quiz_questions'];

	public function transform(QuizSet $quizSet)
	{
		return [
			'id'   => $quizSet->id,
			'name' => $quizSet->name,
		];
	}

	public function includeQuizQuestions(QuizSet $quizSet)
	{
		$questions = $quizSet->questions()->with(['answers'])->get();

		return $this->collection(
			$questions,
			new QuizQuestionTransformer([
				'quiz_sets' => $quizSet->id,
			]),
			'quiz_questions'
		);
	}
}
