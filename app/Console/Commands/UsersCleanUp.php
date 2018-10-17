<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UsersCleanUp extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'users:cleanup';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

	/**
	 * Create a new command instance.
	 *
	 * @return void
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
		$orphanes = User::with(['time'])
			->where(function ($query) {
				$query
					->whereDoesntHave('orders', function ($query) {
						$query
							->where('paid', 1);
					})
					->orWhereHas('orders', function ($query) {
						$query->whereHas('coupon', function ($query) {
							$query
								->where('value', 100)
								->where('type', 'percentage');
						});
					});
			})
			->doesntHave('roles')
			->get();

		$usersCount = $orphanes->count();
		$this->showTable($orphanes);
		$this->info("Found {$usersCount} users");

		return;
	}

	private function showTable($users) {
		$headers = ['id', 'name', 'email', 'time'];
		$rows = [];

		foreach ($users as $user) {
			$rows[] = [
				$user->id,
				$user->full_name,
				$user->email,
				$user->time->max('time'),
			];
		}

		$this->table($headers, $rows);
	}
}
