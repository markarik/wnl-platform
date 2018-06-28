<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Cache;

class RoleAssign extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'role:assign {role} {users}';

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
		$role = Role::where('name', $this->argument('role'))->first();
		if (!$role) {
			$this->warn('Invalid role name');

			return 42;
		}

		$users = $this->argument('users');
		if ($users === 'all') {
			$users = User::all();
		} else {
			$users = User::whereIn('id', explode(',', $users))->get();
		}

		if ($users->count() === 0) {
			$this->warn('No users found. Make sure second argument is whether a comma separated list of user IDs or "all".');

			return 42;
		}

		foreach ($users as $user) {
			$user->roles()->attach($role);
			Cache::tags("user-{$user->id}")->flush();
		}

		$this->call('user:migrate-subscription', ['--admins' => true]);

		$this->info('OK.');

		return 42;
	}
}
