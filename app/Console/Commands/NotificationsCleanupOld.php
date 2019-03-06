<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Carbon\Carbon;

class NotificationsCleanupOld extends CommandWithMonitoring
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'notifications:cleanup-old {--force}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Remove notifications older than 3 weeks';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handleCommand()
	{
		$notifications = Notification::whereDate('created_at', '<', Carbon::now()->subWeeks(3));

		if (
			!$this->option('force') &&
			!$this->confirm("About to remove {$notifications->count()} notifications. Are you sure?")
		) {
			$this->info("Aborting...");
			die;
		}

		$notifications->delete();
		$this->info("Done!");
	}
}
