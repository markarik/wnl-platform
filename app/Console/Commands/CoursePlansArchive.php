<?php

namespace App\Console\Commands;

use App\Jobs\ArchiveCoursePlan;
use App\Models\User;
use App\Models\UserLesson;
use Carbon\Carbon;

class CoursePlansArchive extends CommandWithMonitoring
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'coursePlans:archive';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Copy user course plans that have been lately changed.';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handleCommand()
	{
		$users = UserLesson::select('user_id')
			->whereBetween('updated_at', [Carbon::yesterday(), Carbon::today()])
			->groupBy('user_id')
			->get()
			->pluck('user_id');

		foreach ($users as $userId) {
			dispatch_now(new ArchiveCoursePlan(User::find($userId), false));
		}

		return;
	}
}
