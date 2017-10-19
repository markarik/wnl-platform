<?php

namespace App\Console\Commands;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Console\Command;

class LessonUser extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'lesson:user 
    						{action : allow or deny}
    						{lesson : lesson ID}
    						{--U|users= : User ID or comma separated list of user IDs}
    						{--R|role= : Pass role name to grant/revoke access to all users having this role}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Manage user access to lessons';

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
		$users = $this->option('users');
		$role = $this->option('role');
		$lesson = Lesson::find($this->argument('lesson'));
		$action = $this->argument('action');

		if (!$users && !$role) {
			$this->error('Provide either --users or --role option to continue.');
			exit;
		}

		if ($users) {
			$users = User::whereIn('id', explode(',', $users))->get();
		}

		if ($role) {
			$users = User::ofRole($role);
		}

		if ($users->count() < 1) {
			$this->warn('No users found');
			exit;
		}

		if ($action === 'deny') {
			foreach ($users as $user) {
				$user->lessonsAccess()->detach($lesson);
			}
		} elseif ($action === 'allow') {
			foreach ($users as $user) {
				$user->lessonsAccess()->attach($lesson);
			}
		} else {
			$this->error('Invalid action name provided.');
		}

		$this->info('OK.');

		return;
	}
}
