<?php
namespace App\Console;


use GuzzleHttp\Exception\GuzzleException;

class PrometheusPushgateway
{
	public function notify($metricName)
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
			$client->request('POST', env('PUSHGATEWAY_URL') . '/metrics/job/laravel-schedule/instance/'. config('app.instance_name'), [
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
