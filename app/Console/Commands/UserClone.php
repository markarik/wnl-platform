<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\PrivateApi\UserStateApiController;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class UserClone extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'user:clone {sourceUserId} {--course=1} {--table=}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Copy user data to another user';

	/**
	 *  Default tables to be cloned.
	 *
	 * @var array
	 */
	private static $TABLES = [
		'exams_results',
		'reactables',
		'role_user',
		'user_quiz_results',
		'user_settings',
		'users_plan_progress',
		'users_plans',
		'user_lesson',
		'user_subscription',
	];

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		list ($targetUser, $sourceUser, $courseId) = $this->collectInput();
		$redis = Redis::connection();

		if ($table = $this->option('table')){
			$this->copyTable($table, $sourceUser->id, $targetUser->id);
			die;
		}

		$this->copyRedisData($redis, $targetUser, $sourceUser, $courseId);

		foreach (self::$TABLES as $table) {
			$this->copyTable($table, $sourceUser->id, $targetUser->id);
		}

		$this->copyTable('user_course_progress', $sourceUser->profile->id, $targetUser->profile->id);

		return;
	}

	private function collectInput()
	{
		$courseId = $this->option('course');
		$sourceUserId = $this->argument('sourceUserId');
		$sourceUser = User::find($sourceUserId);

		if (!$sourceUser) {
			$this->error('Source user not found.');
			die;
		}

		if ($this->confirm('Create new user?')) {
			$targetUser = $this->createUser();
		} else {
			$targetUserId = $this->ask('Target user ID');
			$targetUser = User::find($targetUserId);
		}

		if (!$targetUser) {
			$this->error('Target user not found.');
			die;
		}

		return [$targetUser, $sourceUser, $courseId];
	}

	private function copyRedisData($redis, $targetUser, $sourceUser, $courseId)
	{
		$courseProgress = $redis->get(UserStateApiController::getCourseRedisKey($sourceUser->profile->id, $courseId));
		$redis->set(UserStateApiController::getCourseRedisKey($targetUser->profile->id, $courseId), $courseProgress);
		$lessonKeys = $redis->keys(UserStateApiController::getLessonRedisKey($sourceUser->profile->id, $courseId, '*'));
		foreach ($lessonKeys as $key) {
			$lessonProgress = $redis->get($key);
			$lessonProgressObj = json_decode($lessonProgress);
			$lessonId = $lessonProgressObj->route->params->lessonId;
			$redis->set(UserStateApiController::getLessonRedisKey($targetUser->profile->id, $courseId, $lessonId), $lessonProgress);
		}
	}

	private function copyTable($table, $sourceUserId, $targetUserId)
	{
		\DB::beginTransaction();

		try {
			$results = \DB::table($table)->where('user_id', $sourceUserId)->get();
			$insert = $results->map(function ($row) use ($targetUserId) {
				$row->user_id = $targetUserId;
				unset($row->id);
				return (array)$row;
			})->toArray();

			foreach ($insert as $item) {
				try {
					\DB::table($table)->insert($item);
				} catch (\Illuminate\Database\QueryException $e) {
					if ($e->errorInfo[1] === 1062) {
						// Means entry is duplicated.
					} else {
						throw $e;
					}
				}
			}
		} catch (\Exception $e) {
			$this->error($e->getMessage());
			die;
		}

		\DB::commit();
	}

	private function createUser()
	{
		$pass = $this->ask('Password', str_random(8));
		$user = factory(User::class)->create(['password' => bcrypt($pass)]);
		$this->info("Login: {$user->email}\n" . "Password: {$pass}\n\n");
		$role = Role::byName('test');
		$user->roles()->attach($role);

		$coupon = factory(Coupon::class)->create([
			'value' => 100,
		]);
		$order = $user->orders()->create([
			'product_id' => Product::slug(Product::SLUG_WNL_ONLINE)->id,
			'method' => 'free',
			'paid_amount' => 0,
			'paid_at' => Carbon::now(),
		]);
		$order->paid = true;
		$order->coupon_id = $coupon->id;
		$order->save();

		return $user;
	}
}
