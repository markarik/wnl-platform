<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\QuizQuestion;
use App\Http\Controllers\Api\ApiTransformer;

class QuizQuestionTransformer extends ApiTransformer
{
	protected $availableIncludes = ['quiz_answers', 'comments'];
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(QuizQuestion $quizQuestion)
	{
		$data = [
			'id'             => $quizQuestion->id,
			'text'           => $quizQuestion->text,
			'explanation'    => $quizQuestion->explanation,
			'preserve_order' => $quizQuestion->preserve_order,
			'tags'           => $quizQuestion->tags,
			'total_hits'     => $quizQuestion->answers->sum('hits'),
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		if (self::shouldInclude('reactions')) {
			$data = array_merge($data, ReactionsCountTransformer::transform($quizQuestion));
		}

		return $data;
	}

	public function includeQuizAnswers(QuizQuestion $quizQuestion)
	{
		$answers = $quizQuestion->answers;

		return $this->collection(
			$answers,
			new QuizAnswerTransformer([
				'quiz_questions' => $quizQuestion->id,
			]),
			'quiz_answers'
		);
	}

	public function includeComments(QuizQuestion $quizQuestion)
	{
		$comments = $quizQuestion->comments;

		return $this->collection(
			$comments,
			new CommentTransformer([
				'quiz_questions' => $quizQuestion->id,
			]),
			'comments'
		);
	}
}
