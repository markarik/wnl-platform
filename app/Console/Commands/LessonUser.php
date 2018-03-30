<?php

namespace App\Console\Commands;

use App\Models\Group;
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
							{--A|all : Apply for all lessons}
							{--L|lessons= : lesson ID or comma separated list of lesson IDs}
							{--G|groups= : A group of lessons to be affected (group ID or comma separated list)}
							{--U|users= : User ID or comma separated list of user IDs}
							{--R|role= : Pass role name to grant/revoke access to all users having this role}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Manage user access to lessons';

	protected $allowedActions = ['allow', 'deny'];

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
		$action = $this->argument('action');

		if (!in_array($action, $this->allowedActions)) {
			$this->error('Invalid action name provided.');
			exit;
		}

		if ($this->option('all')) {
			$lessons = Lesson::all()->pluck('id')->toArray();
		} elseif ($this->option('groups')) {
			$groupsIds = explode(',', $this->option('groups'));
			$lessons = [];

			foreach($groupsIds as $groupId) {
				$lessons = $this->addLessonsFromGroup($groupId, $lessons);
			}
		} else {
			$lessons = explode(',', $this->option('lessons'));
		}

		if (empty($lessons)) {
			$this->error('No lessons found for the given arguments. Try different ones.');
			exit;
		}

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

		$lessonsAccess = array_map(function($lesson) use ($action) {
			$access = $action === 'allow' ? 1 : 0;
			return ['access' => $access];
		}, array_flip($lessons));

		foreach ($users as $user) {
			$user->lessonsAccess()->syncWithoutDetaching($lessonsAccess);
		}

		$this->info('OK.');

		return;
	}

	private function addLessonsFromGroup($groupId, $lessons) {
		$group = Group::find($groupId);

		if (!$group) {
			return $lessons;
		}

		return array_unique(
			array_merge($group->lessons()->pluck('id')->toArray(), $lessons)
		, SORT_NUMERIC);
	}
}
