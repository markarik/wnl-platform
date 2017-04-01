<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use \Illuminate\Http\Request;

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

	use AuthenticatesUsers {
		login as protected originalLogin;
	}

	/**
	 * Where to redirect users after login.
	 *
	 * @var string
	 */
	protected $redirectTo = 'app/course/1';

	/**
	 * Create a new controller instance.
	 */
	public function __construct()
	{
		$this->middleware('guest', ['except' => 'logout']);
	}

	/**
	 * Handle a login request to the application.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
	 */
	public function login(Request $request)
	{
		if (app()->environment('demo')) {
			return $this->demoLogin($request);
		}

		return $this->originalLogin($request);
	}

	/**
	 * Show the application's login form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function showLoginForm()
	{
		if (app()->environment('demo')) {
			\Cookie::forget('XSRF-TOKEN');
			\Cookie::forget('laravel_session');
			return view('auth.demo-login');
		}

		return view('auth.login');
	}

	/**
	 * Log the user out of the application.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
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
	 * Handle login in demo environment
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function demoLogin(Request $request)
	{
		$user = User::create([
			'first_name' => $request->get('first_name'),
			'last_name'  => $request->get('last_name'),
			'email'      => str_random() . '@wiecejnizlek.pl',
			'password'   => 'secret',
		]);

		Auth::login($user);

		return redirect(self::redirectPath());
	}


}
