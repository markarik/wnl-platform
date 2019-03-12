<?php

return [

	// Default browser window position
	'default_position' => [
		'x' => env('DUSK_WINDOW_X', 0),
		'y' => env('DUSK_WINDOW_Y', 0),
	],

	// Default browser window size
	'custom_size'     => [
		'width'  => env('DUSK_WINDOW_WIDTH', 960),
		'height' => env('DUSK_WINDOW_HEIGHT', 1080),
	],

	'desktop_size'     => [
		'width'  => env('DUSK_WINDOW_WIDTH', 1280),
		'height' => env('DUSK_WINDOW_HEIGHT', 1080),
	],

	'tablet_size'     => [
		'width'  => env('DUSK_WINDOW_WIDTH', 1024),
		'height' => env('DUSK_WINDOW_HEIGHT', 1080),
	],

	'screen-size' => env('DUSK_SIZE_PRESET', 'desktop_size'),

	// Basic auth settings
	// (set up only if target app requires authentication)
	'auth'             => [
		'enabled'  => env('DUSK_AUTH', false),
		'login'    => env('DUSK_LOGIN'),
		'password' => env('DUSK_PASSWORD'),
	],
];
