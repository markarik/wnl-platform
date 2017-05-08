<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
				'APP_USE_LOCAL_STORAGE' => env('APP_USE_LOCAL_STORAGE'),
				'SENTRY_DSN_VUE_PUB'    => env('SENTRY_DSN_VUE_PUB'),
			],
			'config' => [
				'papi' => config('papi'),
			],
		]);

		return view('layouts.admin');
	}
}
