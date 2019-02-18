<?php

namespace App\Jobs;

use App\Models\QuizAnswer;
use App\Models\Taxonomy;
use App\Models\QuizQuestion;
use App\Models\ExamResults;
use App\Http\Controllers\Api\Filters\ByTaxonomy\SubjectsFilter;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


/**
 * @property bool mail
 */
class CalculateExamResults implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	protected $examId;
	protected $userId;
	protected $answers;

	/**
	 * Create a new job instance.
	 *
	 * @param Order $order
	 * @param bool $proforma
	 * @param bool $send
	 *
	 * @internal param bool $mail
	 */
	public function __construct($examId, $userId, $answers)
	{
		$this->examId = $examId;
		$this->userId = $userId;
		$this->answers = $answers;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$examQuestions = QuizQuestion::whereHas('tags', function($tag) {
			$tag->where('tags.id', $this->examId);
		})->get()->pluck('id')->toArray();

		$resolvedLekQuestions = collect($this->answers)
			->map(function($result) {
				$correct = QuizAnswer::find($result['quiz_answer_id'])->is_correct;
				$result['is_correct'] = $correct;

				return $result;
			});

		$correctlyAnswered = $resolvedLekQuestions->filter(function($value) {
			return $value['is_correct'];
		});

		$txTags = Taxonomy::where('name', 'subjects')->first()->rootTagsFromTaxonomy();
		$tagIds = $txTags->pluck('tag_id');
		$filter = app(SubjectsFilter::class);
		$quizQuestion = app(QuizQuestion::class);

		$totalAggregated = collect(
			$filter->fetchAggregationByIds(
				$quizQuestion,
				$examQuestions,
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

		$subjects = [];

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

		ExamResults::create([
			'correct' => $correctlyAnswered->count(),
			'correct_percentage' => $correctlyAnswered->count() / count($examQuestions) * 100,
			'resolved' => $resolvedLekQuestions->count(),
			'resolved_percentage' => $resolvedLekQuestions->count() / count($examQuestions) * 100,
			'exam_tag_id' => $this->examId,
			'user_id' => $this->userId,
			'total' => count($examQuestions),
			'details' => json_encode(['subjects' => $subjects])
		]);

		\Log::notice("Successfully Calculated Exam Results");
	}
}
