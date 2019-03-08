<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\PrivateApi\UserStateApiController;
use App\Models\UserCourseProgress;
use App\Models\Lesson;
use App\Models\User;
use Facades\App\Contracts\CourseProvider;
use Closure;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class RemoveProgress extends Command
{
	/**
	* The name and signature of the console command.
	*
	* @var string
	*/
	protected $signature = 'progress:remove {user}';

	/**
	* The console command description.
	*
	* @var string
	*/
	protected $description = 'Remove user\'s progress from MySQL and Redis';
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
		$profileId = User::find($passedUserId)->profile->id;

		$this->transaction(function () use ($profileId, $passedUserId) {
			$courseId = CourseProvider::getCourseId();
			$courseKey = UserStateApiController::getCourseRedisKey($profileId, $courseId);
			$lessonsKeys = Lesson::all()->pluck('id')->map(function($item) use ($courseId, $profileId) {
				return UserStateApiController::getLessonRedisKey($profileId, $courseId, $item);
			});
			$bar = $this->output->createProgressBar($lessonsKeys->count());

			$lessonsKeys->each(function($lessonKey) use ($bar) {
				Redis::del($lessonKey);
				$bar->advance();
			});

			Redis::del($courseKey);
			UserCourseProgress::where('user_id', $passedUserId)->delete();

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
