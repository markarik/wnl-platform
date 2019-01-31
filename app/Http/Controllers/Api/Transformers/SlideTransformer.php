<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Slide;
use App\Http\Controllers\Api\ApiTransformer;

class SlideTransformer extends ApiTransformer
{
	protected $parent;
	protected $withContext;
	protected $availableIncludes = ['quiz_questions', 'taxonomy_terms'];

	public function __construct($parent = null, $withContext = false)
	{
		$this->parent = $parent;
		$this->withContext = $withContext;
	}

	public function transform(Slide $slide)
	{
		$data = [
			'content'       => $slide->content,
			'is_functional' => $slide->is_functional,
			'snippet'       => $slide->snippet,
			'id'            => $slide->id
		];

		if (!empty($this->withContext) || self::shouldInclude('context')) {
			$searchable = $slide->toSearchableArray();
			$data['context'] = !empty($searchable) ? $searchable['context'] : [];
		}

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}

	public function includeQuizQuestions(Slide $slide)
	{
		$questions = $slide->quizQuestions;

		return $this->collection(
			$questions,
			new QuizQuestionTransformer([
				'slides' => $slide->id,
			]),
			'quiz_questions'
		);
	}

	public function includeTaxonomyTerms(Slide $slide)
	{
		return $this->collection(
			$slide->taxonomyTerms,
			new TaxonomyTermTransformer(['slides' => $slide->id]),
			'taxonomy_terms'
		);
	}
}
