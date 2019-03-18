<?php

namespace App\Http\Controllers\Payment;


use App\Http\Forms\SignUpForm;
use App\Mail\SignUpConfirmation;
use App\Models\Product;
use App\Models\User;
use App\Traits\CheckoutTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Kris\LaravelFormBuilder\FormBuilderTrait;

class AccountController
{
	use CheckoutTrait;
	use FormBuilderTrait;

	public function index(Request $request)
	{
		$user = Auth::user();
		$product = $this->getProduct($request);

		if (
			!$product instanceof Product ||
			!$product->available ||
			$product->signups_close->isPast() ||
			$product->signups_start->isFuture()
		) {
			return view('payment.signups-closed', ['product' => $product]);
		}

		$coupon = $this->readCoupon($user);

		if ($user) {
			if ($user->signUpComplete) {
				return view('payment.account-name', [
					'user' => $user,
					'product' => $product,
					'productPriceWithCoupon' => $product->getPriceWithCoupon($coupon),
					'coupon' => $coupon,
				]);
			} else {
				return view('payment.account-continue', [
					'product' => $product,
					'productPriceWithCoupon' => $product->getPriceWithCoupon($coupon),
					'coupon' => $coupon,
				]);
			}
		}

		// Set intended url after successful login
		$request->session()->flash('url.intended', route('payment-personal-data'));
		$form = $this->form(SignUpForm::class, [
			'method' => 'POST',
			'url'    => route('payment-account-post'),
		]);

		return view('payment.account-register', [
			'product' => $product,
			'productPriceWithCoupon' => $product->getPriceWithCoupon($coupon),
			'coupon' => $coupon,
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


		return redirect(route('payment-personal-data'));
	}

	protected function readCoupon($user)
	{
		$userCoupon = $user ? $user->coupons->first() : null;
		if (session()->has('coupon')) {
			return session()->get('coupon')->fresh();
		} else {
			return $userCoupon;
		}
	}
}
