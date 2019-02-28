<?php

namespace App\Console\Commands;


use App\Console\PrometheusPushgateway;
use Illuminate\Console\Command;

abstract class CommandWithMonitoring extends Command
{
	public function handle()
	{
		$result = $this->handleBody();

		if (!$result) {
			// don't send notification on command failure

			$metricName = env('MONITORING_METRIC_NAME');
			if ($metricName) {
				(new PrometheusPushgateway())->notify(env('MONITORING_METRIC_NAME'));
			} else {
				\Log::debug('Monitoring notification silenced on non-production env');
			}
		}
		return $result;
	}

	abstract public function handleBody();
}
