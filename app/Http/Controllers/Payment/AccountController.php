<?php

namespace App\Http\Controllers\Payment;


use App\Http\Controllers\Controller;
use App\Http\Forms\SignUpForm;
use App\Mail\SignUpConfirmation;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use App\Traits\CheckoutTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Kris\LaravelFormBuilder\FormBuilderTrait;

class AccountController extends Controller
{
	use CheckoutTrait;
	use FormBuilderTrait;

	public function index(Request $request)
	{
		$user = Auth::user();

		if ($user) {
			$hasCurrentProduct = $this->hasCurrentProduct($request);
			$canBuyAlbum = $this->canBuyAlbum($request);

			if ($hasCurrentProduct && $canBuyAlbum) {
				return view('payment.account-buy-album', [
					'user' => $user
				]);
			}

			if ($hasCurrentProduct && !$canBuyAlbum) {
				return view('payment.account-no-available-product', [
					'user' => $user,
				]);
			}

			if ($user->signUpComplete) {
				return view('payment.account-name', [
					'user' => $user,
				]);
			}

			return view('payment.account-continue');
		}

		// Set intended url after successful login
		$request->session()->flash('url.intended', route('payment-personal-data'));
		$form = $this->form(SignUpForm::class, [
			'method' => 'POST',
			'url'    => route('payment-account-post'),
		]);

		return view('payment.account-register', [
			'form'    => $form,
		]);
	}

	public function handleRegister(Request $request)
	{
		$form = $this->form(SignUpForm::class);

		if (!$form->isValid()) {
			Log::notice('Sing up form invalid, redirecting...');

			return redirect()->back()->withErrors($form->getErrors())->withInput();
		}

		Log::notice('Creating user account');
		$user = User::create(
			[
				'email'              => $request->get('email'),
				'password'           => bcrypt($request->get('password')),
				'consent_terms'      => 1,
			]
		);

		Mail::to($user)->send(new SignUpConfirmation($user));

		Auth::login($user);
		Log::debug('User automatically logged in after registration.');


		return redirect(route('payment-personal-data', ['slug' => Product::SLUG_WNL_ONLINE]));
	}
}
