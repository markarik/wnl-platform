<?php

namespace App\Console\Commands;

use App\Models\UserLesson;
use App\Models\User;
use Illuminate\Console\Command;

class SetUsersLessons extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'user:set-user-lessons';

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
		$users = User::all();
		$bar = $this->output->createProgressBar($users->count());

		foreach($users as $user) {
			$userOrders = $user
				->orders()
				->where('paid', 1)
				->whereIn('product_id', [9,10])
				->where('orders.canceled', '<>', 1)
				->get();

			foreach($userOrders as $order) {
				$lessons = $order->product->lessons;

				$lessonsWithStartDate = $lessons->map(function($item) use ($user) {
					if ($item->isAccessible($user)) {
						return null;
					}

					return [
						'lesson_id' => $item->id,
						'start_date' => $item->pivot->start_date,
						'user_id' => $user->id
					];
				})->filter()->toArray();

				UserLesson::insert($lessonsWithStartDate);
			}
			$bar->advance();
		}
		$bar->finish();
		print "\n";
		return true;
	}
}
