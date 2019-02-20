<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Forms\SignUpForm;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Rules\ValidatePassportNumber;
use App\Rules\ValidatePersonalIdentityNumber;

class PersonalDataController extends Controller
{
	use FormBuilderTrait;

	public function index(FormBuilder $formBuilder, $productSlug = null)
	{
		$request = app(Request::class);

		if ($productSlug !== null) {
			$product = Product::slug($productSlug);

			if ($product instanceof Product) {
				Session::put('product', $product);
			}
		}

		$product = Session::get('product');
		if (!$product instanceof Product || !$product->available
			|| $product->signups_close->isPast() || $product->signups_start->isFuture()) {
			return redirect()->route('payment-select-product');
		}

		if (Auth::check() && !$request->edit) {
			$this->createOrder(Auth::user(), $request);

			return redirect()->route('payment-personal-data', ['?edit=true']);
		}

		$form = $this->form(SignUpForm::class, [
			'method' => 'POST',
			'url'    => route('payment-personal-data-post'),
			'model'  => Auth::user(),
		])->modify('password', 'password', [
			'value' => '',
		]);

		session()->flash('url.intended', route('payment-personal-data'));

		return view('payment.personal-data', [
			'form'    => $form,
			'product' => $product,
		]);

	}

	public function handle(Request $request)
	{
		$form = $this->form(SignUpForm::class);

		$validator = $this->getIdentityNumberValidator($request->get('identity_number_type'));
		if (!is_object($validator)) {
			// Very strange situation,
			// somebody probably tried to do something nasty.
			return redirect()->back()->withInput();
		}

		$validations = ['identity_number' => $validator];

		$user = Auth::user();
		if ($user) {
			// Don't require email and pass when updating order/account data.
			$validations = array_merge($validations, [
				'email'                 => 'email',
				'password'              => '',
				'password_confirmation' => ''
			]);
		}

		$form->validate($validations);

		if (!$form->isValid()) {
			Log::notice('Sing up form invalid, redirecting...');

			return redirect()->back()->withErrors($form->getErrors())->withInput();
		}

		if ($user) {
			$this->updateAccount($user, $request);
			$this->updateOrder($user, $request);
		} else {
			$user = $this->createAccount($request);
			$this->createOrder($user, $request);
		}

		return redirect(route('payment-confirm-order'));
	}

	protected function createOrder($user, $request)
	{
		\Log::notice('Creating order');
		$order = $user->orders()->create([
			'product_id' => Session::get('product')->id,
			'session_id' => str_random(32),
			'invoice'    => $request->invoice ?? $user->invoice ?? 0,
		]);

		$userCoupon = $user->coupons->first();
		if (session()->has('coupon')) {
			$coupon = session()->get('coupon')->fresh();
			$this->addCoupon($order, $coupon);
		} elseif ($userCoupon) {
			$this->addCoupon($order, $userCoupon);
		} elseif ($order->product->slug !== 'wnl-album') {
			$this->generateStudyBuddy($order);
		}
	}

	protected function generateStudyBuddy($order)
	{
		$expires = Carbon::now()->addYears(1);
		$coupon = Coupon::create([
			'name'         => 'Study Buddy',
			'type'         => 'amount',
			'value'        => 100,
			'expires_at'   => $expires,
			'code'         => strtoupper(str_random(7)),
			'times_usable' => 0,
		]);

		$coupon->products()->attach(
			Product::whereIn('slug', ['wnl-online', 'wnl-online-onsite'])->get()
		);

		$order->studyBuddy()->create([
			'code' => $coupon->code,
		]);
	}

	protected function createAccount($request)
	{
		Log::notice('Creating user account');
		$user = User::create(
			[
				'first_name'         => $request->get('first_name'),
				'last_name'          => $request->get('last_name'),
				'email'              => $request->get('email'),
				'password'           => bcrypt($request->get('password')),
				'invoice'            => $request->get('invoice') ?? 0,
				'invoice_name'       => $request->get('invoice_name'),
				'invoice_nip'        => $request->get('invoice_nip'),
				'invoice_address'    => $request->get('invoice_address'),
				'invoice_zip'        => $request->get('invoice_zip'),
				'invoice_city'       => $request->get('invoice_city'),
				'invoice_country'    => $request->get('invoice_country'),
				'consent_newsletter' => $request->get('consent_newsletter') ?? 0,
				'consent_account'    => $request->get('consent_account') ?? 0,
				'consent_order'      => $request->get('consent_order') ?? 0,
				'consent_terms'      => $request->get('consent_terms') ?? 0,
			]
		);

		$user->userAddress()->create([
			'street'    => $request->get('address'),
			'zip'       => $request->get('zip'),
			'city'      => $request->get('city'),
			'phone'     => $request->get('phone'),
			'recipient' => $request->get('recipient'),
		]);

		$user->personalData()->create(
			$this->getIdentityNumbersArray($request)
		);

		Auth::login($user);
		Log::debug('User automatically logged in after registration.');

		return $user;
	}

	protected function updateAccount($user, $request)
	{
		$user->update([
			'first_name' => $request->first_name ?? $user->first_name,
			'last_name' => $request->last_name ?? $user->last_name,
			'email' => $request->email ?? $user->email,
			'invoice_name' => $request->invoice_name ?? $user->invoice_name,
			'invoice_nip' => $request->invoice_nip ?? $user->invoice_nip,
			'invoice_address' => $request->invoice_address ?? $user->invoice_address,
			'invoice_zip' => $request->invoice_zip ?? $user->invoice_zip,
			'invoice_city' => $request->invoice_city ?? $user->invoice_city,
			'invoice_country' => $request->invoice_country ?? $user->invoice_country,
			'consent_terms' => $request->consent_terms ?? $user->consent_terms,
			'consent_newsletter' => $request->consent_newsletter ?? $user->consent_newsletter,
		]);

		$user->userAddress()->updateOrCreate(
		['user_id' => $user->id],
		[
			'street'    => $request->get('address'),
			'zip'       => $request->get('zip'),
			'city'      => $request->get('city'),
			'phone'     => $request->get('phone'),
			'recipient' => $request->get('recipient'),
		]);

		$user->personalData()->updateOrCreate(
			['user_id' => $user->id],
			$this->getIdentityNumbersArray($request)
		);
	}

	protected function updateOrder($user, $request)
	{
		Log::notice('Updating order');
		$order = $user->orders()
			->recent()
			->update([
				'product_id' => Session::get('product')->id,
				'session_id' => str_random(32),
				'invoice'    => $request->invoice ?? $user->invoice ?? 0,
			]);
	}

	protected function addCoupon($order, $coupon)
	{
		if ($coupon->products->count() > 0 &&
			!$coupon->products->contains($order->product)
		) {
			return;
		}

		if ($coupon->times_usable < 1 &&
			$coupon->times_usable !== null
		) {
			return;
		}

		$order->attachCoupon($coupon);
	}

	protected function getIdentityNumberValidator($identityNumberType) {
		$validators = [
			'passport_number' => new ValidatePassportNumber,
			'personal_identity_number' => new ValidatePersonalIdentityNumber,
		];

		if (!array_key_exists($identityNumberType, $validators)) {
			return false;
		}

		return $validators[$identityNumberType];
	}

	protected function getIdentityNumbersArray(Request $request) {
		$identityNumbers = [
			'passport_number' => null,
			'personal_identity_number' => null,
		];

		if (array_key_exists($request->get('identity_number_type'), $identityNumbers)) {
			$identityNumbers[$request->get('identity_number_type')] = $request->get('identity_number');
		}

		return $identityNumbers;
	}
}
