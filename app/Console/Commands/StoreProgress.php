<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\PrivateApi\UserStateApiController;
use App\Models\UserCourseProgress;
use Facades\App\Contracts\CourseProvider;
use Closure;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class StoreProgress extends CommandWithMonitoring
{
	/**
	* The name and signature of the console command.
	*
	* @var string
	*/
	protected $signature = 'progress:store {user?}';

	/**
	* The console command description.
	*
	* @var string
	*/
	protected $description = 'Store users progress in MySQL';
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
	public function handleCommand()
	{
		// IMPORTANT!!!! user id is in fact profile id
		$passedUserId = $this->argument('user');
		$this->transaction(function () use ($passedUserId) {
			$courseId = CourseProvider::getCourseId();

			if (empty($passedUserId)) {
				$keyPattern = UserStateApiController::getCourseRedisKey('*', $courseId);
				$allKeys = $this->redis->keys($keyPattern);
				$bar = $this->output->createProgressBar(count($allKeys));

				foreach ($allKeys as $key) {
					if (count(explode(':', $key)) === 5) {
						$userId = $this->extractUserIdFromKey($key);
						$this->storeProgress($key, $userId, $bar);
					}
				}
			} else {
				$key = UserStateApiController::getCourseRedisKey($passedUserId, $courseId);
				$bar = $this->output->createProgressBar(1);
				$this->storeProgress($key, $passedUserId, $bar);
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

	private function extractUserIdFromKey($key)
	{
		return explode(':', $key)[3];
	}

	private function storeProgress($key, $userId, $bar)
	{
		$lessonsProgressRaw = $this->redis->get($key);

		if (!empty($lessonsProgressRaw)) {
			$lessonsProgress = json_decode($lessonsProgressRaw);

			foreach ($lessonsProgress as $lessonId => $lessonData) {
				if ($lessonId !== 'undefined') {
					$lessonKey = UserStateApiController::getLessonRedisKey($userId, 1, $lessonId);
					$lessonProgressRaw = $this->redis->get($lessonKey);

					if (empty($lessonProgressRaw)) continue;
					$lessonProgress = json_decode($lessonProgressRaw);

					if (empty($lessonProgress)) continue;

					$model = UserCourseProgress::firstOrNew([
						'user_id' => $userId,
						'lesson_id' => $lessonId,
						'screen_id' => null,
						'section_id' => null,
					]);

					$model->status = $lessonData->status;
					$model->save();
					foreach($lessonProgress as $key => $value) {
						if ($key === 'screens') {
							if (is_array($value)) {
								// Filter out `null` values introduced due to bug: PLAT-1264
								$value = array_filter($value, function($val) { return $val !== null; } );
							}

							forEach($value as $screenId => $screenData) {
								$model = UserCourseProgress::firstOrNew([
									'user_id' => $userId,
									'lesson_id' => $lessonId,
									'screen_id' => $screenId,
									'section_id' => null,
								]);

								$model->status = $screenData->status ?? 'in-progress';
								$model->save();

								forEach($screenData as $key => $value) {
									if ($key === 'sections') {
										if (is_array($value)) {
											// Filter out `null` values introduced due to bug: PLAT-1264
											$value = array_filter($value, function($val) { return $val !== null; } );
										}

										forEach($value as $sectionId => $sectionData) {
											UserCourseProgress::firstOrCreate([
												'user_id' => $userId,
												'lesson_id' => $lessonId,
												'screen_id' => $screenId,
												'section_id' => $sectionId,
												'status' => 'complete',
											]);
										}
									}
								}
							}
						}
					}
				}
				$bar->advance();
			}
		}
	}
}
