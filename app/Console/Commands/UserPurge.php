<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UserPurge extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'user:purge {ids*}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Hard-delete user record and all user-related data for fiven user IDs';

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
		$ids = $this->argument('ids');
		$headers = ['id', 'name', 'time', 'roles', 'created', 'last session'];

		foreach ($ids as $id) {
			$user = User::find($id);

			if (!$user) {
				$this->warn("User #{$id} not found.");
				continue;
			}

			$data = [
				$user->id,
				$user->full_name,
				ceil($user->userTime->max('time') / 60) . 'h',
				$user->roles->pluck('name')->implode(','),
				$user->created_at,
				$user->sessions->last()->created_at ?? '',
			];

			$this->table($headers, [$data]);

			if ($this->confirm('Confirm deleting above user and all related user data')) {
				$this->purge($user);
				$this->info('User deleted');
			} else {
				continue;
			}
		}

		return;
	}

	public function purge($user) {

		$user->orders()->delete();
		$user->coupons()->delete();
		$user->profile()->delete();
		$user->personalData()->delete();
		$user->billing()->delete();
		$user->settings()->delete();
		$user->userAddress()->delete();
		$user->roles()->delete();
		$user->chatMessages()->delete();
		$user->notifications()->delete();
		$user->sessions()->delete();
		$user->comments()->delete();
		$user->tasks()->delete();
		$user->qnaAnswers()->delete();
		$user->lessonsAvailability()->delete();
		$user->reactables()->delete();
		$user->chatRooms()->delete();
		$user->subscription()->delete();
		$user->userTime()->delete();
		$user->delete();

		return;
	}
}
