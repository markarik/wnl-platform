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
		$this->load(__DIR__.'/Commands');
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
			->command('chat:archive-messages')
			->hourly();

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
			->command('time:store')
			->dailyAt('01:30');

		$schedule
			->command('slides:fromCategory')
			->dailyAt('00:30');

		$schedule
			->command('progress:store')
			->dailyAt('02:30');

		$schedule
			->command('quiz:slackDaysDecrement')
			->dailyAt('02:30');

		$schedule
			->command('role:assignFromProducts edition-2-participant 5,6')
			->everyFiveMinutes();
	}
}
