<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Default Log Channel
	|--------------------------------------------------------------------------
	|
	| This option defines the default log channel that gets used when writing
	| messages to the logs. The name specified in this option should match
	| one of the channels defined in the "channels" configuration array.
	|
	*/

	'default' => env('LOG_CHANNEL', 'single'),

	/*
	|--------------------------------------------------------------------------
	| Log Channels
	|--------------------------------------------------------------------------
	|
	| Here you may configure the log channels for your application. Out of
	| the box, Laravel uses the Monolog PHP logging library. This gives
	| you a variety of powerful log handlers / formatters to utilize.
	|
	| Available Drivers: "single", "daily", "slack", "syslog",
	|                    "errorlog", "monolog",
	|                    "custom", "stack"
	|
	*/

	'channels' => [
		'single' => [
			// Don't use daily driver until we figure out the permissions
			// Currently we run the container using root (because of New Relic) and to avoid permissions error we do:
			// `RUN touch storage/logs/laravel.log` in Dockerfile.php
			// daily driver uses date in filenames so this workaround doesn't work
			'driver' => 'single',
			'path' => storage_path('logs/laravel.log'),
			'level' => env('APP_LOG_LEVEL', 'debug'),
		],
	],

];
