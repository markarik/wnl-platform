<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserQuizResults;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use App\Models\Taxonomy;
use App\Http\Controllers\Api\Filters\ByTaxonomy\SubjectsFilter;
use App\Models\ExamResults;

class ExamsResults extends Command
{
	const LEK_TAG_ID = 505;
	const QUESTIONS_IN_EXAM = 200;
	const MUST_MATCH = 120;

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'exams:results {user?}';

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

		if (!empty($userId)) {
			$results = $this->getUserResults($userId);
			$user = User::find($userId);

			if (!empty($results)) {
				$this->saveInDB($userId, $results);
				$this->printResults([[$userId, $user->fullName, $results['correct_perc'], $results['resolved_perc']]]);
			}

		} else {
			$rows = [];
			$allUsers = User::all();
			$bar = $this->output->createProgressBar($allUsers->count());

			foreach($allUsers as $user) {
				$results = $this->getUserResults($user->id);

				if (!empty($results)) {
					$this->saveInDB($user->id, $results);
					$rows[] = [$user->id, $user->fullName, $results['correct_perc'], $results['resolved_perc']];
				}

				$bar->advance();
			}

			$bar->finish();

			$this->printResults($rows);
		}
	}

	protected function getUserResults($userId) {
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

		if ($key === false) {
			return [];
		}

		$resolvedLekQuestions = $userQuizResults
			->slice($key, self::QUESTIONS_IN_EXAM)
			->filter(function($value) use ($lekQuestions) {
				return in_array($value->quiz_question_id, $lekQuestions);
			})
			->map(function($result) {
				$correct = QuizAnswer::find($result->quiz_answer_id)->is_correct;
				$result->is_correct = $correct;

				return $result;
			});

		$correctlyAnswered = $resolvedLekQuestions->filter(function($value) {
			return $value->is_correct;
		});

		$txTags = Taxonomy::where('name', 'subjects')->first()->rootTagsFromTaxonomy();
		$tagIds = $txTags->pluck('tag_id');
		$filter = app(SubjectsFilter::class);
		$quizQuestion = app(QuizQuestion::class);

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
				$resolvedLekQuestions->pluck('quiz_question_id')->toArray(),
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
				'correct'            => $correct,
				'correct_perc'       => $correct / $total * 100,
				'resolved'           => $resolved,
				'resolved_perc'      => $resolved / $total * 100
			];
		}

		return [
			'subjects' => $subjects,
			'correct'  => $correctlyAnswered->count(),
			'correct_perc' => $correctlyAnswered->count() / self::QUESTIONS_IN_EXAM * 100,
			'resolved' => $resolvedLekQuestions->count(),
			'resolved_perc' => $resolvedLekQuestions->count() / self::QUESTIONS_IN_EXAM * 100
		];
	}

	protected function printResults($rows) {
		$this->table(
			['user_id', 'full name', '% correct', '% resolved'],
			$rows
		);
	}

	protected function saveInDB($userId, $results) {
		ExamResults::firstOrCreate(['user_id' => $userId, 'exam_tag_id' => self::LEK_TAG_ID], [
			'correct' => $results['correct'],
			'correct_percentage' => $results['correct_perc'],
			'resolved' => $results['resolved'],
			'resolved_percentage' => $results['resolved_perc'],
			'exam_tag_id' => self::LEK_TAG_ID,
			'details' => json_encode([
				'subjects' => $results['subjects']
			]),
			'total' => self::QUESTIONS_IN_EXAM
		]);
	}
}
