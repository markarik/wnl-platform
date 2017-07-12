<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Reaction;
use App\Models\QuizQuestion;

class MarkWrongQuestionsAsBookmarked extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'bookmark:quiz';

	private $now, $insertValuesBase;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->now = Carbon::now();
		$this->insertValuesBase = [
			'reaction_id' => Reaction::type('bookmark')->id, // bookmark
			'reactable_type' => 'App\\Models\\QuizQuestion',
			'created_at' => $this->now,
			'updated_at' => $this->now,
		];
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$wrongResults = \DB::table('user_quiz_results')
			->select('user_quiz_results.quiz_question_id as reactable_id', 'user_quiz_results.user_id')
			->join('quiz_answers', 'quiz_answers.id', '=', 'user_quiz_results.quiz_answer_id')
			->where('quiz_answers.is_correct', 0)
			->get()
			->toArray();

		$insertValues = array_map([$this, 'composeInsertValues'], $wrongResults);

		\DB::table('reactables')->insert($insertValues);
	}

	private function composeInsertValues($result) {
		return array_merge((array) $result, $this->insertValuesBase);
	}
}
