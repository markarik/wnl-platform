<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserQuizResults;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use App\Models\Taxonomy;

use App\Http\Controllers\Api\Filters\ByTaxonomy\SubjectsFilter;

class ExamsResults extends Command
{
	const LEK_TAG_ID = 505;
	const QUESTIONS_IN_EXAM = 200;
	const MUST_MATCH = 15;

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'exams:results {user}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Calculate exams results';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$userId = $this->argument('user');
		$lekQuestions = QuizQuestion::whereHas('tags', function($tag) {
			$tag->where('tags.id', self::LEK_TAG_ID);
		})->get()->pluck('id')->toArray();
		$userQuizResults = UserQuizResults::where('user_id', $userId)->get();

		$key = $userQuizResults->search(function($item, $key) use ($lekQuestions, $userQuizResults) {
			for ($i = 0; $i < self::MUST_MATCH; $i++) {
				if (!empty($userQuizResults->get($key + $i)) && in_array($userQuizResults->get($key + $i)->quiz_question_id, $lekQuestions)) {
					// pass
				} else {
					return false;
				}
			}

			return true;
		});

		$lekResults = $userQuizResults
			->slice($key, self::QUESTIONS_IN_EXAM)
			->filter(function($value) use ($lekQuestions) {
				return in_array($value->quiz_question_id, $lekQuestions);
			});

		$lekResults->map(function($result) {
			$correct = QuizAnswer::find($result->quiz_answer_id)->is_correct;

			return $result->is_correct = $correct;
		});

		$txTags = Taxonomy::where('name', 'subjects')->first()->rootTagsFromTaxonomy();
		$tagIds = $txTags->pluck('tag_id');
		$filter = app(SubjectsFilter::class);
		$quizQuestion = app(QuizQuestion::class);
		$correctlyAnswered = $lekResults->filter(function($value) {
			return $value->is_correct;
		});

		$totalAggregated = collect(
			$filter->fetchAggregationByIds(
				$quizQuestion,
				$lekQuestions,
				$tagIds
			)
		)->keyBy('key');

		$correctAggregated = collect(
			$filter->fetchAggregationByIds(
				$quizQuestion,
				$correctlyAnswered->pluck('quiz_question_id')->toArray(),
				$tagIds,
				false
			)
		)->keyBy('key');

		$resolvedAggregated = collect(
			$filter->fetchAggregationByIds(
				$quizQuestion,
				$lekResults->pluck('quiz_question_id')->toArray(),
				$tagIds,
				false
			)
		)->keyBy('key');


		foreach ($txTags as $txTag) {
			$correct = $correctAggregated->get($txTag->tag_id)['doc_count'] ?? 0;
			$total = $totalAggregated->get($txTag->tag_id)['doc_count'];
			$resolved = $resolvedAggregated->get($txTag->tag_id)['doc_count'];

			$subjects[] = [
				'total'              => $total,
				'name'               => $txTag->tag->name,
				'resolved'           => $resolved,
				'resolved_perc'      => $resolved / $total * 100,
				'correct'            => $correct,
				'correct_perc'       => $correct / $total * 100,
			];
		}

		dd([
			'subjects' => $subjects,
			'correct'  => $correctlyAnswered->count(),
			'correct_perc' => $correctlyAnswered->count() / self::QUESTIONS_IN_EXAM * 100,
			'resolved' => $lekResults->count(),
			'resolved_perc' => $lekResults->count() / self::QUESTIONS_IN_EXAM * 100
		]);
	}
}
