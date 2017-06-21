<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JavaScript;

class AppController extends Controller
{
	public function index()
	{
		JavaScript::put([
			'env'    => [
				'appDebug'              => env('APP_DEBUG'),
				'APP_LOG_LEVEL'         => env('APP_LOG_LEVEL'),
				'appEnv'                => env('APP_ENV'),
				'appUrl'                => env('APP_URL'),
				'chatHost'              => env('CHAT_HOST'),
				'chatPort'              => env('CHAT_PORT'),
				'SENTRY_DSN_VUE_PUB'    => env('SENTRY_DSN_VUE_PUB'),
				'FIRST_LESSON_ID'       => env('FIRST_LESSON_ID')
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
