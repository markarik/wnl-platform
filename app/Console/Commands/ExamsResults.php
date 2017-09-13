<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserQuizResults;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;

class ExamsResults extends Command
{
	const LEK_TAG_ID = 505;
	const QUESTIONS_IN_EXAM = 200;

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
			return in_array($item->quiz_question_id, $lekQuestions)
				&& !empty($userQuizResults->get($key + 1)) && in_array($userQuizResults->get($key + 1)->quiz_question_id, $lekQuestions)
				&& !empty($userQuizResults->get($key + 2)) && in_array($userQuizResults->get($key + 2)->quiz_question_id, $lekQuestions)
				&& !empty($userQuizResults->get($key + 3)) && in_array($userQuizResults->get($key + 3)->quiz_question_id, $lekQuestions)
				&& !empty($userQuizResults->get($key + 4)) && in_array($userQuizResults->get($key + 4)->quiz_question_id, $lekQuestions)
				&& !empty($userQuizResults->get($key + 5)) && in_array($userQuizResults->get($key + 5)->quiz_question_id, $lekQuestions)
				&& !empty($userQuizResults->get($key + 6)) && in_array($userQuizResults->get($key + 6)->quiz_question_id, $lekQuestions)
				&& !empty($userQuizResults->get($key + 7)) && in_array($userQuizResults->get($key + 7)->quiz_question_id, $lekQuestions)
				&& !empty($userQuizResults->get($key + 8)) && in_array($userQuizResults->get($key + 8)->quiz_question_id, $lekQuestions)
				&& !empty($userQuizResults->get($key + 9)) && in_array($userQuizResults->get($key + 9)->quiz_question_id, $lekQuestions)
				&& !empty($userQuizResults->get($key + 10)) && in_array($userQuizResults->get($key + 10)->quiz_question_id, $lekQuestions)
				&& !empty($userQuizResults->get($key + 11)) && in_array($userQuizResults->get($key + 11)->quiz_question_id, $lekQuestions)
				&& !empty($userQuizResults->get($key + 12)) && in_array($userQuizResults->get($key + 12)->quiz_question_id, $lekQuestions)
				&& !empty($userQuizResults->get($key + 13)) && in_array($userQuizResults->get($key + 13)->quiz_question_id, $lekQuestions)
				&& !empty($userQuizResults->get($key + 14)) && in_array($userQuizResults->get($key + 14)->quiz_question_id, $lekQuestions);
		});

		$lekResults = $userQuizResults->slice($key, self::QUESTIONS_IN_EXAM);

		$lekResults->map(function($result) {
			$correct = QuizAnswer::find($result->quiz_answer_id)->is_correct;

			return $result->is_correct = $correct;
		});
	}
}
