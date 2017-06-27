<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\PrivateApi\User\UserStateApiController;
use App\Models\UserTime;
use Closure;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class StoreTime extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'time:store {user?}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Store users time on platform in MySQL';
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

		if (empty($passedUserId)) {
			$keyPattern = UserStateApiController::getUserTimeRedisKey('?');
		} else {
			$keyPattern = UserStateApiController::getUserTimeRedisKey($passedUserId);
		}

		$this->transaction(function () use ($keyPattern, $passedUserId) {
			if ($passedUserId) {
				$this->storeTime($keyPattern, $passedUserId);
			}

			$allKeys = $this->redis->keys($keyPattern);

			foreach ($allKeys as $key) {
				$userId = $this->extractUserIdFromKey($key);
				$this->storeTime($key, $userId);
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
		return explode(':', $key)[2];
	}

	private function storeTime($key, $userId)
	{
		$timeRaw = $this->redis->get($key);

		if (!empty($timeRaw)) {
			$time = json_decode($timeRaw);

			$userTime = UserTime::firstOrNew([
				'user_id' => $userId
			]);

			$userTime->time = $time;
			$userTime->save();
		}
	}
}
