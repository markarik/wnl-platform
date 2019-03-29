<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
				'ECHO_HOST'          => env('ECHO_HOST', env('APP_URL')),
				'ECHO_PORT'          => env('ECHO_PORT', 8755),
				'SENTRY_DSN_VUE_PUB' => env('SENTRY_DSN_VUE_PUB'),
				'MODERATORS_CHANNEL' => env('MODERATORS_CHANNEL', 3),
				'appVersion'         => config('app.version'),
				'appInstanceName'    => config('app.instance_name'),
			],
			'config' => [
				'papi' => config('papi'),
			],
			'course' => [
				'productLogo' => config('course.product_logo'),
			],
		]);

		return view('layouts.admin');
	}
}
