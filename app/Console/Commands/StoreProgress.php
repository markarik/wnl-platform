<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\PrivateApi\User\UserStateApiController;
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

		$allKeys = $this->redis->keys($keyPattern);

		foreach ($allKeys as $key) {
			if (empty($userId)) {
				$userId = $this->extractUserIdFromKey($key);
			}
			$lessonsProgressRaw = $this->redis->get($key);
			$lessonsProgress = json_decode($lessonsProgressRaw);

			var_dump($lessonsProgress);
		}

		return;
	}

	private function transaction(Closure $callback)
	{
		DB::beginTransaction();
		$this->redis->multi();

		try {
			$callback();
		} catch (Exception $e) {
			DB::rollBack();
			$this->redis->discard();
			throw $e;
		}

		DB::commit();
		$this->redis->exec();
	}

	private function extractUserIdFromKey($key)
	{
		return explode(':', $key)[3];
	}
}
