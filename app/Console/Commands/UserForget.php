<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UserForget extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'user:forget {ids*}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Soft-delete user record and anonymize user-related data for fiven user IDs';

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
			/** @var User|null $user */
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

			if ($this->confirm('Confirm deleting above user')) {
				$user->forget();
				$this->info('User deleted');
			} else {
				continue;
			}
		}

		return;
	}
}
