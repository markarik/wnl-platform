<?php

namespace App\Http\Controllers\Auth;

use App;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Session;

class LoginController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles authenticating users for the application and
	| redirecting them to your home screen. The controller uses a trait
	| to conveniently provide its functionality to your applications.
	|
	*/

	use AuthenticatesUsers;

	/**
	 * Where to redirect users after login.
	 *
	 * @var string
	 */
	protected $redirectTo = 'app/courses/1';

	/**
	 * Create a new controller instance.
	 */
	public function __construct()
	{
		$this->middleware('guest', ['except' => 'logout']);
	}

	/**
	 * Log the user out of the application.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function logout(Request $request)
	{
		$this->guard()->logout();

		$request->session()->flush();

		$request->session()->regenerate();

		$request->session()->flash('logout', true);

		return redirect('/login');
	}

	/**
	 * The user has been authenticated.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  mixed $user
	 *
	 * @return mixed
	 */
	protected function authenticated(Request $request, $user)
	{
		if ($user->suspended) {
			$request->session()->put('suspended', true);
		}

		$this->singleSessionCheck($user);
	}

	protected function singleSessionCheck($user)
	{
		if (App::environment(['testing', 'dev'])) return;

		$redis = Redis::connection('session');
		foreach ($user->sessions as $session) {
			$redis->del('laravel:' . $session->session_id);
		}

		$user->sessions()->delete();

		$user->sessions()->create([
			'session_id' => Session::getId(),
		]);
	}
}
