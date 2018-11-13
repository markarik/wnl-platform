<?php

namespace App\Console\Commands;

use App\Models\Role;
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
	protected $description = 'Filter out user accounts';

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
		$roleNames = Role::all()->pluck('name')->toArray();
		$users = User::with(['userTime'])
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

		$usersCount = $users->count();
		$this->showTable($users);
		$this->info("Found {$usersCount} users");

		for ($i = 0; $i < $usersCount; $i++) {
			$this->showTable([$users[$i]]);
			$action = $this->choice('Choose action', [
				1 => 'assign role',
				2 => 'remove from db',
				3 => 'skip',
				4 => 'back',
				5 => 'preview changes',
				6 => 'commit changes'
			]);

			switch ($action) {
				case 'assign role':
					$role = $this->anticipate('Role name', $roleNames);
					$users[$i]->action = 'assign';
					$users[$i]->roleToAssign = $role;
					break;

				case 'remove from db':
					$users[$i]->action = 'remove';
					break;

				case 'skip' :
					continue;
					break;

				case 'back' :
					$i -= 2;
					break;

				case 'preview changes' :
					$this->showTable($users);
					break;

				case 'commit changes' :
					$this->apply($users);
					break;
			}
		}

		return;
	}

	private function showTable($users)
	{
		$headers = ['id', 'name', 'email', 'time', 'action'];
		$rows = [];

		foreach ($users as $user) {
			$rows[] = [
				$user->id,
				$user->full_name,
				$user->email,
				$user->userTime->max('time'),
				$user->action ?? ' ' . $user->roleToAssign ?? '',
			];
		}

		$this->table($headers, $rows);
	}

	private function apply($users)
	{
		$this->info('Applying changes...');
		foreach ($users as $user) {
			if (empty($user->action)) continue;

			if ($user->action === 'remove') {
				(new UserPurge())->purge($user);
				$this->info("Removed user {$user->id} from database.");
			}

			if ($user->action === 'assign') {
				\Artisan::call('role:assign', [
					'role' => $user->roleToAssign,
					'users' => $user->id,
				]);

				$this->info("Assigned role {$user->roleToAssign} to user {$user->id}");
			}
		}

		die;
	}
}
