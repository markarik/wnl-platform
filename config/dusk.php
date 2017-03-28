<?php

return [

	// Default browser window position
	'default_position' => [
		'x' => env('DUSK_WINDOW_X', 0),
		'y' => env('DUSK_WINDOW_Y', 0),
	],

	// Default browser window size
	'default_size'     => [
		'width'  => env('DUSK_WINDOW_WIDTH', 960),
		'height' => env('DUSK_WINDOW_HEIGHT', 1080),
	],

];
