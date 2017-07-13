<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Reactable;
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

		$added = $ignored = 0;
		echo "\n";
		$bar = $this->output->createProgressBar(count($wrongResults));

		foreach ($wrongResults as $result) {
			try {
				$insertValues = array_merge((array) $result, $this->insertValuesBase);
				\DB::table('reactables')->insert($insertValues);
				$added++;
			} catch (\PDOException $e) {
				$ignored++;
			}

			$bar->advance();
		}

		$bar->finish();
		$this->info("\n\nDone! " . $added . ' reactions added, ' . $ignored
		 . " ignored. \n");
	}
}
