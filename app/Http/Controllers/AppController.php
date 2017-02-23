<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JavaScript;

class AppController extends Controller
{
	public function index()
	{
		JavaScript::put([
			'baseURL' => env('APP_URL')
		]);
		return view('layouts.app');
    }
}
