<?php

namespace App\Http\Controllers\Api\PrivateApi;

use App\Events\Quiz\QuizQuestionEdited;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Filters\ByTaxonomy\SubjectsFilter;
use App\Http\Controllers\Api\Filters\Quiz\ResolutionFilter;
use App\Http\Controllers\Api\Transformers\QuizQuestionTransformer;
use App\Http\Requests\Quiz\UpdateQuizQuestion;
use App\Models\ExamResults;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\Slide;
use App\Models\Tag;
use App\Models\Taxonomy;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;

class QuizQuestionsApiController extends ApiController
{
	const AVAILABLE_FILTERS = [
		'quiz-planned',
		'quiz-resolution',
		'quiz-collection',
		'by_taxonomy-subjects',
		'by_taxonomy-exams',
		'search'
	];

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.quiz-questions');
	}

	public function getWithTrashed($id) {
		$quizQuestion = QuizQuestion::withTrashed()->find($id);
		if (!$quizQuestion) {
			return $this->respondNotFound();
		}

		return $this->transformAndRespond($quizQuestion);
	}

	public function post(UpdateQuizQuestion $request)
	{
		$question = QuizQuestion::create([
			'text' => $request->input('question'),
			'explanation' => $request->input('explanation'),
			'preserve_order' => $request->input('preserve_order')
		]);
		$questionId = $question['id'];

		if ($request->has('answers')) {
			foreach($request->answers as $answer) {
				$answerModel = QuizAnswer::create([
					'text' => $answer['text'],
					'is_correct' => $answer['is_correct'],
					'quiz_question_id' => $questionId,
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

		if ($request->has('slides')) {
			$question->slides()->detach();

			foreach ($request->slides as $slideId) {
				$slide = Slide::find($slideId);

				$question->slides()->attach($slide);
			}
		}

		if ($request->has('tags')) {
			$question->tags()->detach();

			foreach ($request->tags as $tag) {
				$tagModel = Tag::firstOrCreate(['id' => $tag['id']]);

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
			'explanation' => $request->input('explanation'),
			'preserve_order' => $request->input('preserve_order'),
			'updated_at' => Carbon::now() // Hack to fire update event
		]);

		$user = Auth::user();

		event(new QuizQuestionEdited($question, $user));

		return $this->respondOk();
	}

	public function delete($id) {
		$quizQuestion = QuizQuestion::find($id);

		if (!$quizQuestion) {
			return $this->respondNotFound();
		}

		$quizQuestion->delete();
		return $this->respondOk();
	}

	public function restore($id) {
		$quizQuestion = QuizQuestion::withTrashed()->find($id);

		if (!$quizQuestion) {
			return $this->respondNotFound();
		}

		$quizQuestion->restore();
		return $this->transformAndRespond($quizQuestion);
	}

	public function stats(Request $request)
	{
		$userId = Auth::user()->id;

		$model = app(QuizQuestion::class);
		$data = $this->getOverall((clone $model), $userId);

		$mockExam = $this->getMockExam((clone $model), $userId);
		if ($mockExam) {
			$data = array_merge($data, $mockExam);
		}

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
		$builder = $this->applyResolution($model, $userId, 'lastly_correct');
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
				$this->applyResolution($model, $userId, 'lastly_correct'),
				$tagIds,
				false
			)
		)->keyBy('key');

		foreach ($txTags as $txTag) {
			$total = $totalAggregated->get($txTag->tag_id)['doc_count'];
			$resolved = $resolvedAggregated->get($txTag->tag_id)['doc_count'] ?? 0;
			$correct = $correctAggregated->get($txTag->tag_id)['doc_count'] ?? 0;

			$subjects[] = [
				'tag_id'             => $txTag->tag_id,
				'name'               => $txTag->tag->name,
				'total'              => $total ?? 0,
				'resolved'           => $resolved,
				'resolved_perc'      => $resolved == 0 ? 0 : $resolved / $total * 100,
				'correct'            => $correct,
				'correct_perc'       => $correct == 0 ? 0 : $correct / $resolved * 100,
				'correct_perc_total' => $total == 0 ? 0 : $correct / $total * 100,
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
		return Taxonomy::where('name', 'subjects')->first()->rootTagsFromTaxonomy();

	}

	protected function getMockExam($model, $userId)
	{
		$userExamResults = ExamResults::where('user_id', $userId)
			->where('resolved', '>', 0)
			->orderBy('created_at', 'desc')
			->get()
			->map(function ($examResults) {

				$subjects = array_map(function($subject) {
					return [
						'name'               => $subject->name,
						'total'              => $subject->total ?? 0,
						'resolved'           => $subject->resolved,
						'resolved_perc'      => $subject->resolved_perc,
						'correct'            => $subject->correct,
						'correct_perc'       => $subject->correct == 0 ? 0 : $subject->correct / $subject->resolved * 100,
						'correct_perc_total' => $subject->correct_perc,
					];
				}, json_decode($examResults->details)->subjects);

				return [
					'total'              => $examResults->total,
					'resolved'           => $examResults->resolved,
					'resolved_perc'      => $examResults->resolved_percentage,
					'correct'            => $examResults->correct,
					'correct_perc'       => $examResults->resolved == 0 ? 0 : $examResults->correct / $examResults->resolved * 100,
					'correct_perc_total' => $examResults->correct_percentage,
					'subjects'           => $subjects,
					'exam_name'          => Tag::find($examResults->exam_tag_id)->name,
					'created_at'         => $examResults->created_at
				];
			});

		if (empty($userExamResults)) {
			return [];
		}

		return [
			'mock_exams' => $userExamResults->toArray()
		];
	}

	protected function getOverall($model, $userId)
	{
		$total = (clone $model)->count();
		$resolved = $this->resolved((clone $model), $userId);
		$correct = $this->correct((clone $model), $userId);

		return [
			'total'              => $total,
			'resolved'           => $resolved,
			'resolved_perc'      => $resolved / $total * 100,
			'correct'            => $correct,
			'correct_perc'       => $resolved == 0 ? 0 : $correct / $resolved * 100,
			'correct_perc_total' => $total == 0 ? 0 : $correct / $total * 100,
			'subjects'           => $this->getSubjectsStats((clone $model), $userId),
		];
	}
}
