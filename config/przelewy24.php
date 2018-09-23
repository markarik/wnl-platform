<?php

return [

	'merchant_id'      => env('P24_MERCHANT_ID'),
	'order_key'        => env('P24_ORDER_KEY'),
	'api_key'          => env('P24_API_KEY'),
	'crc_key'          => env('P24_CRC_KEY'),
	'base_url'         => env('P24_BASE_URL'),
	'api_version'      => '3.2',
	'transaction_path' => '/trnDirect',
	'verify_path'      => '/trnVerify',
	'test_path'        => '/testConnection',
	'transaction_url'  => env('P24_BASE_URL') . '/trnDirect',
	'verify_url'       => env('P24_BASE_URL') . '/trnVerify',
	'status_url'       => env('P24_STATUS_URL'),
];
