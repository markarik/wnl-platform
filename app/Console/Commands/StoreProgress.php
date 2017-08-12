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
		$passedUserId = $this->argument('user');

		$this->transaction(function () use ($passedUserId) {

			if (empty($passedUserId)) {
				$keyPattern = UserStateApiController::getCourseRedisKey('*', 1);
				$allKeys = $this->redis->keys($keyPattern);
				foreach ($allKeys as $key) {
					if (count(explode(':', $key)) === 5) {
						$userId = $this->extractUserIdFromKey($key);
						$this->storeProgress($key, $userId);
					}
				}
			} else {
				$key = UserStateApiController::getCourseRedisKey($passedUserId, 1);
				$this->storeProgress($key, $passedUserId);
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

	private function storeProgress($key, $userId)
	{
		$lessonsProgressRaw = $this->redis->get($key);

		if (!empty($lessonsProgressRaw)) {
			$lessonsProgress = json_decode($lessonsProgressRaw);

			foreach ($lessonsProgress as $lessonId => $lessonData) {
				if ($lessonId !== 'undefined') {
					$model = UserCourseProgress::firstOrNew(
						['lesson_id' => $lessonId, 'user_id' => $userId]
					);
					$model->status = $lessonData->status;
					$model->save();
				}
			}
		}
	}
}
