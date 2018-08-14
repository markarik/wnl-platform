<?php

namespace Demo\App\Http\Controllers\Auth;

use App\Jobs\OrderPaid;
use App\Jobs\PopulateUserCoursePlan;
use App\Models\Product;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
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
	protected $redirectTo = 'app/courses/1';

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
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
	 */
	public function login(Request $request)
	{
		$user = User::create([
			'first_name'         => $request->get('first_name'),
			'last_name'          => ' ',
			'email'              => str_random() . '@wiecejnizlek.pl',
			'password'           => bcrypt('secret'),
			'address'            => '',
			'zip'                => '',
			'city'               => '',
			'phone'              => '',
			'invoice'            => false,
			'invoice_name'       => '',
			'invoice_nip'        => '',
			'invoice_address'    => '',
			'invoice_zip'        => '',
			'invoice_city'       => '',
			'invoice_country'    => '',
			'consent_newsletter' => true,
			'consent_account'    => true,
			'consent_order'      => true,
			'consent_terms'      => true,

		]);

		$demo = Product::slug('wnl-online-demo');

		$order = $user->orders()->create([
			'product_id' => $demo->id,
			'canceled' => 0,
		]);

		$order->paid = true;
		$order->save();

        UserSubscription::updateOrCreate(
            ['user_id' => $user->id],
            ['access_start' => $demo->access_start, 'access_end' => $demo->access_end]
        );

        if ($order->product->lessons->count() > 0) {
            dispatch_now(new PopulateUserCoursePlan($order->user, $order->product));
        }

		Auth::login($user);

		return redirect(self::redirectPath());
	}

	/**
	 * Show the application's login form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function showLoginForm()
	{
		return view('auth.demo-login');
	}

	/**
	 * Log the user out of the application.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
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
}
