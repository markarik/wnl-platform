<?php

namespace App\Console\Commands;

use App\Models\UserPlan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SlackDaysCron extends Command
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
