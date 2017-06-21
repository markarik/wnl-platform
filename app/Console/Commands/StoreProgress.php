<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\PrivateApi\User\UserStateApiController;
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
		$userId = $this->argument('user');

		if (empty($userId)) {
			// get all keys for all users
			$keyPattern = UserStateApiController::getCourseRedisKey('?', 1);
		} else {
			$keyPattern = UserStateApiController::getCourseRedisKey($userId, 1);
		}

		$this->transaction(function() use ($keyPattern) {
			$allKeys = $this->redis->keys($keyPattern);

			foreach ($allKeys as $key) {
				if (empty($userId)) {
					$userId = $this->extractUserIdFromKey($key);
				}
				$lessonsProgressRaw = $this->redis->get($key);

				if (!empty($lessonsProgressRaw)) {
					$lessonsProgress = json_decode($lessonsProgressRaw);

					foreach ($lessonsProgress as $lessonId => $lessonData) {
						$model = UserCourseProgress::firstOrNew(
							['lesson_id' => $lessonId, 'user_id' => $userId]
						);

						$model->status = $lessonData->status;
						$model->save();
					}
				}
			}

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
}
