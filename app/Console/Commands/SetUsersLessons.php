<?php

namespace App\Console\Commands;

use App\Models\UserLesson;
use App\Models\User;
use Illuminate\Console\Command;
use DB;

class SetUsersLessons extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'user:set-user-lessons {user?}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Set user lesson dates based on bought product';

	/**
	 * Create a new command instance.
	 *
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
		$passedUserId = $this->argument('user');
		if (!empty($passedUserId)) {
			$user = User::find($passedUserId);
			$this->setUserLessonBasedOnOrders($user);
		} else {
			$users = User::all();
			$bar = $this->output->createProgressBar($users->count());

			foreach($users as $user) {
				$this->setUserLessonBasedOnOrders($user);
				$bar->advance();
			}
			$bar->finish();
		}
		print "\n";
		return true;
	}

	protected function setUserLessonBasedOnOrders($user) {
		$userOrders = $user
			->orders()
			->where('paid', 1)
			->whereIn('product_id', [9,10])
			->where('orders.canceled', '<>', 1)
			->get();

		DB::beginTransaction();
		foreach($userOrders as $order) {
			$lessons = $order->product->lessons;

			$lessonsWithStartDate = $lessons->map(function($item) use ($user) {
				if ($user->lessonsAvailability->contains($item)) {
					return null;
				}

				return [
					'lesson_id' => $item->id,
					'start_date' => $item->pivot->start_date,
					'user_id' => $user->id
				];
			})->filter()->toArray();

			try {
				UserLesson::insert($lessonsWithStartDate);
			} catch (\PDOException $e) {
				DB::rollback();
				print PHP_EOL;
				$this->error("Failed to save lessons for user $user->id");
				throw $e;
			} catch (Exception $ex) {
				print PHP_EOL;
				DB::rollback();
				$this->error("Failed to save lessons for user $user->id");
				throw $e;
			}
		}
		DB::commit();
	}
}
