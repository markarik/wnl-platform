<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\PrivateApi\UserQuizResultsApiController;
use App\Models\Lesson;
use App\Models\Screen;
use App\Models\User;
use App\Models\UserProfile;
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

	protected $redis;

	protected $cache;

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
		$this->cache = Redis::connection('cache');
		$passedUserId = $this->argument('user');

		if (!$passedUserId) {
			$this->repairRedis();
		}
		$this->repairMysql($passedUserId);

		return;
	}

	private function transaction(Closure $callback)
	{
		DB::beginTransaction();
		$this->redis->multi();

		try {
			$callback();
		}
		catch (Exception $e) {
			DB::rollBack();
			$this->redis->discard();
			throw $e;
		}

		DB::commit();
		$this->redis->exec();
	}

	protected function repairRedis()
	{
		$keyTemplate = UserQuizResultsApiController::KEY_QUIZ_TEMPLATE;;
		$profiles = UserProfile::all();
		$this->info('[Redis] Retrieving keys');
		$keys = collect($this->redis->keys('UserState:Quiz:*'));

		$this->info('[Redis] Moving keys to temp. database');
		$total = count($keys);
		foreach ($keys as $i => $key) {
			$this->cache->set($key, $this->redis->get($key));
			$this->redis->del($key);
			if ($i%500 === 0) print "$i/$total";
			print '.';
		}

		print PHP_EOL;
		$this->info('[Redis] Repairing and saving keys back to original db');
		$total = count($profiles);
		foreach ($profiles as $i => $profile) {
			$userKeys = $keys->filter(function($k) use ($profile){
				return str_is("UserState:Quiz:*:{$profile->id}:*", $k);
			});
			foreach ($userKeys as $oldKey) {
				$keyComponents = explode(':', $oldKey);
				$newKey = sprintf($keyTemplate,
					$keyComponents[2],
					$profile->user_id,
					$keyComponents[4]
				);
				$this->redis->set($newKey, $this->cache->get($oldKey));
				$this->cache->del($oldKey);
			}
			if ($i%100 === 0) print "$i/$total";
			print '.';
		}
	}

	protected function repairMysql($passedUserId)
	{
		$this->info('[MySQL] Gathering data');
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

		$this->info('[MySQL] Updating');
		foreach ($resultsGrouped as $userId => $ids) {
			UserQuizResults
				::whereIn('id', $ids)
				->update(['user_id' => $userId]);
			$bar->advance();
		}

		$bar->finish();
		print PHP_EOL;
	}
}
