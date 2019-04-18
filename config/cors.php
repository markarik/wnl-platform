<?php

return [
	/*
  |--------------------------------------------------------------------------
  | Laravel CORS
  |--------------------------------------------------------------------------
  |
  | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
  | to accept any value.
  |
  */

	'supportsCredentials' => false,
	'allowedOrigins' => [
		'http://wiecejnizlek.pl',
		'https://wiecejnizlek.pl',
		'https://bethink.staging.wpengine.com'
	],
	'allowedOriginsPatterns' => [],
	'allowedHeaders' => ['*'],
	'allowedMethods' => ['*'],
	'exposedHeaders' => [],
	'maxAge' => 600,
];
