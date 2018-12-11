<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\PrivateApi\UserStateApiController;
use App\Models\UserCourseProgress;
use Closure;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class StoreProgress extends Command
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
	public function handle()
	{
		// IMPORTANT!!!! user id is in fact profile id
		$passedUserId = $this->argument('user');

		$this->transaction(function () use ($passedUserId) {

			if (empty($passedUserId)) {
				$keyPattern = UserStateApiController::getCourseRedisKey('*', 1);
				$allKeys = $this->redis->keys($keyPattern);
				$bar = $this->output->createProgressBar(count($allKeys));

				foreach ($allKeys as $key) {
					if (count(explode(':', $key)) === 5) {
						$userId = $this->extractUserIdFromKey($key);
						$this->storeProgress($key, $userId, $bar);
					}
				}
			} else {
				$key = UserStateApiController::getCourseRedisKey($passedUserId, 1);
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

					if (!$lessonProgressRaw) continue;
					$lessonProgress = json_decode($lessonProgressRaw);

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
							forEach($value as $screenId => $screenData) {
								$model = UserCourseProgress::firstOrNew([
									'user_id' => $userId,
									'lesson_id' => $lessonId,
									'screen_id' => $screenId,
									'section_id' => null,
								]);

								$model->status = $lessonData->status;
								$model->save();

								forEach($screenData as $key => $value) {
									if ($key === 'sections') {
										forEach($value as $sectionId => $sectionData) {
											$model = UserCourseProgress::firstOrNew([
												'user_id' => $userId,
												'lesson_id' => $lessonId,
												'screen_id' => $screenId,
												'section_id' => $sectionId
											]);
											// it means it has old shape of data in redis
											// can be removed when redis cleared before 2nd edition
											if (!empty($sectionData) && empty($sectionData->status)) {
												$model->status = $sectionData;
											} else {
												$model->status = $sectionData->status;
											}
											$model->save();
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
