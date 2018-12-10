<?php

namespace App\Http\Controllers;

use App\Exceptions\AppGoneException;
use JavaScript;

class AppController extends Controller
{
	public function index()
	{
		JavaScript::put([
			'env'    => [
				'appDebug'           => env('APP_DEBUG'),
				'APP_LOG_LEVEL'      => env('APP_LOG_LEVEL'),
				'appEnv'             => env('APP_ENV'),
				'appUrl'             => env('APP_URL'),
				'chatHost'           => env('CHAT_HOST'),
				'chatPort'           => env('CHAT_PORT'),
				'ECHO_HOST'          => env('ECHO_HOST', env('APP_URL')),
				'ECHO_PORT'          => env('ECHO_PORT', 8755),
				'SENTRY_DSN_VUE_PUB' => env('SENTRY_DSN_VUE_PUB'),
				'MODERATORS_CHANNEL' => env('MODERATORS_CHANNEL', 3),
				'appVersion'         => config('app.version'),
				'sadHost'            => env('SAD_HOST'),
				'sadPort'            => env('SAD_PORT')
			],
			'config' => [
				'papi'    => config('papi'),
				'lessons' => config('lessons'),
				'payment' => config('payment'),
			],
			'defaultSettings' => config('user-default-settings'),
		]);

		return view('layouts.app');
	}
}
