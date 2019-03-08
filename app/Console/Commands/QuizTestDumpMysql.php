<?php

namespace App\Console\Commands;

use App\Models\Lesson;
use App\Models\Screen;
use App\Models\User;
use App\Models\UserQuizResults;
use Illuminate\Console\Command;

class QuizTestDumpMysql extends Command
{
	protected $redis;
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'quizTest:dumpMysql {mode}';

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
		$mode = $this->argument('mode');

		if ($mode === 'before') {
			$data = $this->beforeProcedure();
		} else {
			$data = $this->afterProcedure();
		}

		\Storage::put("quiz_test_dump_mysql_{$mode}.json", json_encode($data));

		return;
	}

	protected function afterProcedure() {
		$users = User::all();
		$siema = [];

		$total = count($users);
		foreach ($users as $i => $user) {
			$count = UserQuizResults
				::where('user_id', $user->id)
				->count();

			$siema[$user->id] = $count;

			if ($i % 100 === 0) print "$i/$total";
			print '.';
		}

		return $siema;
	}

	protected function beforeProcedure() {
		$users = User::all();
		$siema = [];

		$lessons = Lesson
			::select('id')
			->where(['is_required', 1])
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

		$total = count($users);
		foreach ($users as $i => $user) {
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

			$siema[$user->id] = count($outsideLesson) + count($ids);

			if ($i % 100 === 0) print "$i/$total";
			print '.';
		}

		return $siema;
	}
}
