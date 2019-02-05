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
				$this->notifyPrometheusPushgateway('orders_stats_export');
			});

		$schedule
			->command("scout:import 'App\\Models\\Slide'")
			->dailyAt('00:30')
			->after(function () use ($schedule) {
				Artisan::call('cache:clear', [
					'--tags' => 'courses',
				]);
			})
			->after(function () {
				$this->notifyPrometheusPushgateway('scout_import_slides');
			});

		$schedule
			->command('coursePlans:archive')
			->dailyAt('01:20')
			->after(function () {
				$this->notifyPrometheusPushgateway('course_plans_archive');
			});

		$schedule
			->command('time:store')
			->dailyAt('01:30')
			->after(function () {
				$this->notifyPrometheusPushgateway('time_store');
			});

		$schedule
			->command('progress:store')
			->hourly()
			->withoutOverlapping()
			->after(function () {
				$this->notifyPrometheusPushgateway('progress_store');
			});

		$schedule
			->command('quiz:slackDaysDecrement')
			->dailyAt('02:30')
			->after(function () {
				$this->notifyPrometheusPushgateway('quiz_slack_days_decrement');
			});

		$schedule
			->command('orders:handleUnpaid')
			->twiceDaily(8, 20)
			->after(function () {
				$this->notifyPrometheusPushgateway('orders_handle_unpaid');
			});

		$schedule
			->command('notifications:cleanup-old --force')
			->dailyAt('02:45')
			->after(function () {
				$this->notifyPrometheusPushgateway('notifications_cleanup_old');
			});

		$schedule
			->command('sb:cancel')
			->weekly()
			->after(function () {
				$this->notifyPrometheusPushgateway('sb_cancel');
			});

		$schedule
			->command('invoices:jpk-send')
			->monthlyOn(1, '06:00')
			->after(function () {
				$this->notifyPrometheusPushgateway('invoices_jpk_send');
			});

		$schedule
			->command('data-integrity:check')
			->dailyAt('04:00')
			->after(function () {
				$this->notifyPrometheusPushgateway('data_integrity_check');
			});
	}

	private function notifyPrometheusPushgateway($metricName)
	{
		$client = new \GuzzleHttp\Client();
		try {
			$timestamp = time();
			$bodyLines = [
				"# HELP laravel_schedule_${metricName}_last_success_timestamp_seconds Last success unixtime of Laravel schedule: ${metricName}",
				"# TYPE laravel_schedule_${metricName}_last_success_timestamp_seconds gauge",
				"laravel_schedule_${metricName}_last_success_timestamp_seconds ${timestamp}"
			];
			$body = implode("\n", $bodyLines) . "\n";
			$client->request('POST', env('PUSHGATEWAY_URL') . '/metrics/job/laravel-schedule', [
				'body' => $body
			]);
		} catch (GuzzleException $exception) {
			\Log::error('Sending laravel schedule metric to Prometheus Pushgateway failed', [
				'metricName' => $metricName,
				'exceptionMessage' => $exception->getMessage()
			]);
		}
	}
}
