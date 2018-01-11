<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Screen;
use App\Models\QuizQuestion;
use App\Models\Lesson;
use App\Models\UserQuizResults;
use Closure;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class UserIdProfileIdMessUp extends Command
{
	/**
	* The name and signature of the console command.
	*
	* @var string
	*/
	protected $signature = 'fix:userProfileId {user?}';

	/**
	* The console command description.
	*
	* @var string
	*/
	protected $description = 'Fix wrong user_id stored in UserQuizResults';
	private $redis;

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
		$this->redis = Redis::connection();
		$passedUserId = $this->argument('user');

		$this->transaction(function () use ($passedUserId) {
			$lessons = Lesson
				::select('id')
				// 11 - Próbny Lek
				// 14 - Dodatki
				// 15 - Powtórki
				->whereNotIn('group_id', [11,14,15])
				->get()
				->pluck('id')
				->toArray();

			$quizScreens = Screen
				::selectRaw('meta->"$.resources[0].id" as quiz_set_id')
				->where('type', 'quiz')
				->whereIn('lesson_id', $lessons)
				->get()
				->pluck('quiz_set_id')
				->toArray();

			$quizQuestions = \DB
				::table('quiz_question_quiz_set')
				->select('quiz_question_id')
				->whereIn('quiz_set_id', $quizScreens)
				->groupBy('quiz_question_id')
				->get()
				->pluck('quiz_question_id')
				->toArray();

			$users = User::all();
			$resultsGrouped = [];

			if ($passedUserId) {
				print '*****RUNNING IN TEST MODE****';
				print PHP_EOL;

				$user = User::find($passedUserId);

				$ids = UserQuizResults
					::select('id')
					->where('user_id', $user->profile->id)
					->whereIn('quiz_question_id', $quizQuestions)
					->get()
					->pluck('id')
					->toArray();

				$outsideLesson = UserQuizResults
					::select('id')
					->where('user_id', $user->id)
					->whereNotIn('quiz_question_id', $quizQuestions)
					->get()
					->pluck('id')
					->toArray();

				print count($outsideLesson);
				print '  - Solved Questions inside lessons';
				print PHP_EOL;
				print count($ids);
				print ' - Solved Questions outside lessons';
				print PHP_EOL;

				return;
			}

			$bar = $this->output->createProgressBar($users->count() * 2);

			foreach ($users as $user) {
				$resultsGrouped[$user->id] = UserQuizResults
					::select('id')
					->where('user_id', $user->profile->id)
					->whereIn('quiz_question_id', $quizQuestions)
					->get()
					->pluck('id');

					$bar->advance();
				}

			foreach ($resultsGrouped as $userId => $ids) {
				$userObject = User::find($userId);
				UserQuizResults
					::whereIn('id', $ids)
					->update(['user_id' => $userId]);
				$bar->advance();
			}

			$bar->finish();
			print PHP_EOL;
		});
		return;
	}

	private function transaction(Closure $callback)
	{
		DB::beginTransaction();

		try {
			$callback();
		} catch (Exception $e) {
			DB::rollBack();
			throw $e;
		}

		DB::commit();
	}
}
