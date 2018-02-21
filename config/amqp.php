<?php

return [

	'use' => 'common',

	'properties' => [

		'common' => [
			'host'                => env('AMQP_HOST'),
			'port'                => env('AMQP_PORT'),
			'username'            => env('AMQP_USER'),
			'password'            => env('AMQP_PASS'),
			'vhost'               => '/',
			'exchange'            => 'messages',
			'exchange_type'       => 'direct',
//			'consumer_tag'        => 'consumer',
			'ssl_options'         => [], // See https://secure.php.net/manual/en/context.ssl.php
			'connect_options'     => [], // See https://github.com/php-amqplib/php-amqplib/blob/master/PhpAmqpLib/Connection/AMQPSSLConnection.php
			'queue_properties'    => ['x-ha-policy' => ['S', 'all']],
			'exchange_properties' => [],
			'timeout'             => 0,
		],

	],

];
