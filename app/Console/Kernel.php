<?php

namespace App\Console;

use Artisan;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
	/**
	 * Register the commands for the application.
	 *
	 * @return void
	 */
	protected function commands()
	{
		$this->load(__DIR__ . '/Commands');
	}

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule $schedule
	 *
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule
			->command('orders:statsExport')
			->hourly();

		$schedule
			->command("scout:import 'App\\Models\\Slide'")
			->dailyAt('00:30')
			->after(function () use ($schedule) {
				Artisan::call('cache:clear', [
					'--tags' => 'api,slides,search',
				]);
			})
			->after(function () use ($schedule) {
				Artisan::call('cache:warmup');
			});

		$schedule
			->command('coursePlans:archive')
			->dailyAt('01:20');

		$schedule
			->command('time:store')
			->dailyAt('01:30');

		$schedule
			->command('progress:store')
			->hourly();

		$schedule
			->command('quiz:slackDaysDecrement')
			->dailyAt('02:30');

		// $schedule
		// 	->command('orders:handleUnpaid')
		// 	->twiceDaily(8, 20);

		$schedule
			->command('notifications:cleanup-old --force')
			->dailyAt('02:45');

		$schedule
			->command('sb:cancel')
			->weekly();
	}
}
