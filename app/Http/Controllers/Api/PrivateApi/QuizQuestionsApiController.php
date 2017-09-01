<?php

namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Filters\ByTaxonomy\SubjectsFilter;
use App\Http\Controllers\Api\Filters\Quiz\ResolutionFilter;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use App\Models\Taxonomy;
use Auth;

class QuizQuestionsApiController extends ApiController
{
	const AVAILABLE_FILTERS = [
		'quiz-planned',
		'quiz-resolution',
		'by_taxonomy-subjects',
		'by_taxonomy-exams',
//		'by_taxonomy-tags',
	];

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.quiz-questions');
	}

	public function stats(Request $request)
	{
		$userId = Auth::user()->id;

		$model = app(QuizQuestion::class);
		$total = (clone $model)::count();
		$resolved = $this->resolved($total, $model, $userId);
		$correct = $this->correct($model, $userId);

		$data = [
			'total'         => $total,
			'resolved'      => $resolved,
			'resolved_perc' => $resolved / $total * 100,
			'correct'       => $correct,
			'correct_perc'  => $correct / $total * 100,
			'subjects'      => $this->getSubjectsStats($model, $userId),

		];

		return $this->respondOk($data);
	}

	protected function resolved($total, $model, $userId)
	{
		$builder = $this->applyResolution($model, $userId, 'unresolved');
		$unresolved = $builder->count();
		$resolved = $total - $unresolved;

		return $resolved;
	}

	protected function correct($model, $userId)
	{
		$builder = $this->applyResolution($model, $userId, 'correct');
		$correct = $builder->count();

		return $correct;
	}

	protected function getSubjectsStats($model, $userId)
	{
		$filter = app(SubjectsFilter::class);
		$params = ['user_id' => $userId];

		$txTags = $this->taxonomyTags();
		$tagIds = $txTags->pluck('tag_id');

		$totalAggregated = collect(
			$filter->fetchAggregation($model, $tagIds)
		)->keyBy('key');

		$unresolvedAggregated = collect(
			$filter->fetchAggregation(
				$this->applyResolution($model, $userId, 'unresolved'),
				$tagIds
			)
		)->keyBy('key');

		$correctAggregated = collect(
			$filter->fetchAggregation(
				$this->applyResolution($model, $userId, 'correct'),
				$tagIds
			)
		)->keyBy('key');

		foreach ($txTags as $txTag) {
			$total = $totalAggregated->get($txTag->tag_id)['doc_count'];
			$unresolved = $unresolvedAggregated->get($txTag->tag_id)['doc_count'] ?? 0;
			$resolved = $total - $unresolved;
			$correct = $correctAggregated->get($txTag->tag_id)['doc_count'] ?? 0;
			$subjects[] = [
				'tag_id'        => $txTag->tag_id,
				'name'          => $txTag->tag->name,
				'total'         => $total,
				'resolved'      => $resolved,
				'resolved_perc' => $resolved / $total * 100,
				'correct'       => $correct,
				'correct_perc'  => $correct / $total * 100,
			];
		}

		return $subjects;
	}

	protected function applyResolution($model, $userId, $state)
	{
		$filter = app(ResolutionFilter::class);
		$params = [
			'user_id' => $userId,
			'list'    => [$state],
		];

		return $filter->apply($model, $params);
	}

	protected function taxonomyTags()
	{
		return Taxonomy::select()
			->where('name', 'subjects')
			->first()
			->tagsTaxonomy()
			->with('tag')
			->where('parent_tag_id', 0)
			->get();
	}
}
