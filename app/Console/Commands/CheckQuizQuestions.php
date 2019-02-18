<?php

namespace App\Console\Commands;

use App\Models\QuizQuestion;
use Illuminate\Console\Command;

class CheckQuizQuestions extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'quiz:check';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

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
		$questions = QuizQuestion::with('answers')->get();
		$total = $questions->count();
		$progress = 1;

		foreach ($questions as $question) {
			$this->check($question);
			$this->display($progress++, $total);
		}

		return;
	}

	protected function check($question)
	{
		$answersCount = $question->quizAnswers->count();
		if ($answersCount !== 5) {
			$this->warn(
				"Question {$question->id} has {$answersCount} answers!"
			);
		}

		$correctAnswersCount = $question->quizAnswers
			->where('is_correct', 1)->count();

		if ($correctAnswersCount !== 1) {
			$this->warn(
				"Question {$question->id} has {$correctAnswersCount} correct answers!"
			);
		}
	}

	protected function display($progress, $total)
	{
//		if ($progress % 50 === 0) print "{$progress}/{$total}\n";
	}
}
