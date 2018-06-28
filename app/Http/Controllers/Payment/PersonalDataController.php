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
		if (!$product instanceof Product || !$product->available) {
			return redirect()->route('payment-select-product');
		}

		if (Auth::check() && !$request->edit) {
			$this->createOrder(Auth::user(), $request);

			return redirect()->route('payment-confirm-order');
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

		$user = Auth::user();
		if ($user) {
			// Don't require email and pass when updating order/account data.
			$form->validate([
				'email'                 => 'email',
				'password'              => '',
				'password_confirmation' => '',
			]);
		}

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

		$user->userAddress()->firstOrCreate([
			'street'    => $request->get('address'),
			'zip'       => $request->get('zip'),
			'city'      => $request->get('city'),
			'phone'     => $request->get('phone'),
			'recipient' => $request->get('recipient'),
		]);

		Auth::login($user);
		Log::debug('User automatically logged in after registration.');

		return $user;
	}

	protected function updateAccount($user, $request)
	{
		$user->update($request->all());
		$user->userAddress()->update([
			'street'    => $request->get('address'),
			'zip'       => $request->get('zip'),
			'city'      => $request->get('city'),
			'phone'     => $request->get('phone'),
			'recipient' => $request->get('recipient'),
		]);
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
}
