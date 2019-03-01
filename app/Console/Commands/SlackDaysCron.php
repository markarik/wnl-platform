<?php

namespace App\Console\Commands;

use App\Models\UserPlan;
use Carbon\Carbon;

class SlackDaysCron extends CommandWithMonitoring
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'quiz:slackDaysDecrement';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handleCommand()
	{
		$today = Carbon::today();

		UserPlan::whereDoesntHave('questionsProgress', function ($query) {
			$query->where('resolved_at', Carbon::yesterday());
		})
			->where('slack_days_left', '>', 0)
			->where('start_date', '<', $today)
			->where('end_date', '>', $today)
			->decrement('slack_days_left');
	}
}
