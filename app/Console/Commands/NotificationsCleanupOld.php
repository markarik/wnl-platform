<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotificationsCleanupOld extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'notifications:cleanup-old';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Remove notifications older than 3 weeks';

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
		$notifications = Notification::whereDate('created_at', '<', Carbon::now()->subWeeks(3));

		if (!$this->confirm("About to remove {$notifications->count()} notifications. Are you sure?")) {
			$this->info("Aborting...");
			die;
		}

		$notifications->delete();
		$this->info("Done!");
	}
}
