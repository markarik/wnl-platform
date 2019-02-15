<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\QuizQuestion;

class QuizQuestionTransformer extends ApiTransformer
{
	protected $availableIncludes = ['quiz_answers', 'comments', 'slides', 'taxonomy_terms'];
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
			'deleted_at'     => $quizQuestion->deleted_at,
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
		$answers = $quizQuestion->quizAnswers;

		return $this->collection(
			$answers,
			new QuizAnswerTransformer([
				'quiz_questions' => $quizQuestion->id,
			]),
			'quiz_answers'
		);
	}

	public function includeSlides(QuizQuestion $quizQuestion)
	{
		$slides = $quizQuestion->slides;

		return $this->collection($slides, new SlideTransformer([
			'quiz_questions' => $quizQuestion->id
		], true), 'slides');
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

	public function includeTaxonomyTerms(QuizQuestion $quizQuestion)
	{
		return $this->collection(
			$quizQuestion->taxonomyTerms,
			new TaxonomyTermTransformer(['quiz_questions' => $quizQuestion->id]),
			'taxonomy_terms'
		);
	}
}
