<?php

namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use App\Models\Tag;
use App\Http\Requests\Quiz\UpdateQuizQuestion;
use App\Http\Controllers\Api\Transformers\QuizQuestionTransformer;
use League\Fractal\Resource\Item;
use App\Http\Controllers\Api\Filters\ByTaxonomy\SubjectsFilter;
use App\Http\Controllers\Api\Filters\Quiz\ResolutionFilter;
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

	public function post(UpdateQuizQuestion $request)
	{
		$question = QuizQuestion::create(['text' => $request->input('question')]);
		$questionId = $question['id'];

		if ($request->has('answers')) {
			foreach($request->answers as $answer) {
				$answerModel = QuizAnswer::create([
					'text' => $answer['text'],
					'is_correct' => $answer['is_correct'],
					'quiz_question_id' => $questionId,
					'preserve_order' => $request->input('preserve_order')
				]);
			}
		}

		$resource = new Item($question, new QuizQuestionTransformer, $this->resourceName);
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function put(UpdateQuizQuestion $request)
	{
		$question = QuizQuestion::find($request->route('id'));

		if (!$question) {
			return $this->respondNotFound();
		}

		if ($request->has('tags')) {
			$question->tags()->detach();

			foreach($request->tags as $tag) {
				$tagModel = Tag::firstOrCreate(['id' => $tag['id'] ]);

				$question->tags()->attach($tagModel);
			}
		}

		if ($request->has('answers')) {
			foreach($request->answers as $answer) {
				$answerModel = QuizAnswer::find($answer['id']);

				$answerModel->update([
					'text' => $answer['text'],
					'is_correct' => $answer['is_correct']
				]);
			}
		}

		$question->update([
			'text' => $request->input('question'),
			'preserve_order' => $request->input('preserve_order')
		]);

		return $this->respondOk();
	}

	public function stats(Request $request)
	{
		$userId = Auth::user()->id;

		$model = app(QuizQuestion::class);
		$total = (clone $model)::count();
		$resolved = $this->resolved($model, $userId);
		$correct = $this->correct($model, $userId);

		$data = [
			'total'         => $total,
			'resolved'      => $resolved,
			'resolved_perc' => $resolved / $total * 100,
			'correct'       => $correct,
			'correct_perc'  => $resolved == 0 ? 0 : $correct / $resolved * 100,
			'subjects'      => $this->getSubjectsStats($model, $userId),
		];

		return $this->respondOk($data);
	}

	protected function resolved($model, $userId)
	{
		$builder = $this->applyResolution($model, $userId, 'resolved');
		$resolved = $builder->count();

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

		$txTags = $this->taxonomyTags();
		$tagIds = $txTags->pluck('tag_id');

		$totalAggregated = collect(
			$filter->fetchAggregation($model, $tagIds)
		)->keyBy('key');

		$resolvedAggregated = collect(
			$filter->fetchAggregation(
				$this->applyResolution($model, $userId, 'resolved'),
				$tagIds,
				false
			)
		)->keyBy('key');

		$correctAggregated = collect(
			$filter->fetchAggregation(
				$this->applyResolution($model, $userId, 'correct'),
				$tagIds,
				false
			)
		)->keyBy('key');

		foreach ($txTags as $txTag) {
			$total = $totalAggregated->get($txTag->tag_id)['doc_count'];
			$resolved = $resolvedAggregated->get($txTag->tag_id)['doc_count'] ?? 0;
			$correct = $correctAggregated->get($txTag->tag_id)['doc_count'] ?? 0;

			$subjects[] = [
				'tag_id'        => $txTag->tag_id,
				'name'          => $txTag->tag->name,
				'total'         => $total,
				'resolved'      => $resolved,
				'resolved_perc' => $resolved / $total * 100,
				'correct'       => $correct,
				'correct_perc'  => $resolved == 0 ? 0 : $correct / $resolved * 100,
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
