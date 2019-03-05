<?php

namespace App\Console;

use Artisan;
use GuzzleHttp\Exception\GuzzleException;
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
			->hourly()
			->withoutOverlapping()
			->after(function () {
				(new PrometheusPushgateway())->notify('orders_stats_export');
			});

		$schedule
			->command("scout:import 'App\\Models\\Slide'")
			->dailyAt('00:30')
			->after(function () {
				Artisan::call('cache:clear', [
					'--tags' => 'courses',
				]);
			})
			->after(function () {
				(new PrometheusPushgateway())->notify('scout_import_slides');
			});

		$schedule
			->command('coursePlans:archive')
			->dailyAt('01:20')
			->after(function () {
				(new PrometheusPushgateway())->notify('course_plans_archive');
			});

		$schedule
			->command('time:store')
			->dailyAt('01:30')
			->after(function () {
				(new PrometheusPushgateway())->notify('time_store');
			});

		$schedule
			->command('progress:store')
			->hourly()
			->withoutOverlapping()
			->after(function () {
				(new PrometheusPushgateway())->notify('progress_store');
			});

		$schedule
			->command('quiz:slackDaysDecrement')
			->dailyAt('02:30')
			->after(function () {
				(new PrometheusPushgateway())->notify('quiz_slack_days_decrement');
			});

		$schedule
			->command('orders:handleUnpaid')
			->twiceDaily(8, 20)
			->after(function () {
				(new PrometheusPushgateway())->notify('orders_handle_unpaid');
			});

		$schedule
			->command('notifications:cleanup-old --force')
			->dailyAt('02:45')
			->after(function () {
				(new PrometheusPushgateway())->notify('notifications_cleanup_old');
			});

		$schedule
			->command('sb:cancel')
			->weekly()
			->after(function () {
				(new PrometheusPushgateway())->notify('sb_cancel');
			});

		$schedule
			->command('invoices:jpk-send')
			->monthlyOn(1, '06:00')
			->after(function () {
				(new PrometheusPushgateway())->notify('invoices_jpk_send');
			});

		$schedule
			->command('data-integrity:check')
			->dailyAt('04:00')
			->after(function () {
				(new PrometheusPushgateway())->notify('data_integrity_check');
			});
	}
}
