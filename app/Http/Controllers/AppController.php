<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JavaScript;

class AppController extends Controller
{
	public function index()
	{
		JavaScript::put([
			'env'      => [
				'appDebug' => env('APP_DEBUG'),
				'appEnv'   => env('APP_ENV'),
				'appUrl'   => env('APP_URL'),
				'chatHost' => env('CHAT_HOST'),
				'chatPort' => env('CHAT_PORT'),
			],
			'config'   => [
				'papi'    => config('papi'),
				'lessons' => config('lessons'),
			],
		]);

		return view('layouts.app');
	}
}
