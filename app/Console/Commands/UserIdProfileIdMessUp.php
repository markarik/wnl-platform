<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\PrivateApi\UserStateApiController;
use App\Models\UserCourseProgress;
use App\Models\Screen;
use App\Models\QuizQuestion;
use App\Models\Lesson;
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
		$this->redis = Redis::connection();
	}

	/**
	* Execute the console command.
	*
	* @return mixed
	*/
	public function handle()
	{
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
				::selectRaw('meta->"$.resources[0].id"')
				->where('type', 'quiz')
				->whereIn('lesson_id', $lessons)
				->get()
				->pluck('meta->"$.resources[0].id"')
				->toArray();

			$quizQuestions = \DB
				::table('quiz_question_quiz_set')
				->select('quiz_question_id')
				->whereIn('quiz_set_id', array_keys($quizScreens))
				->groupBy('quiz_question_id')
				->get();

			dd($quizQuestions->count());
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
