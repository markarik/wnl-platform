<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Forms\PersonalDataForm;
use App\Models\Coupon;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Rules\ValidatePassportNumber;
use App\Rules\ValidatePersonalIdentityNumber;

class PersonalDataController extends Controller
{
	use FormBuilderTrait;

	public function index(Request $request)
	{
		if (Session::has('product')) {
			$product = Session::get('product');
		} else {
			$product = Product::slug($request->route('productSlug') ?? 'wnl-online');
			Session::put('product', $product);
		}

		if (!$product instanceof Product ||
			!$product->available ||
			$product->signups_close->isPast() ||
			$product->signups_start->isFuture()
		) {
			return view('payment.signups-closed', ['product' => $product]);
		}

		$form = $this->setupForm();

		return view('payment.personal-data', [
			'form'    => $form,
			'product' => $product,
		]);
	}

	public function handle(Request $request)
	{
		$form = $this->setupForm();

		$validations = [];
		$validator = $this->getIdentityNumberValidator($request->get('identity_number_type'));
		if (!is_object($validator)) {
			if (!$form->getField('identity_number')->getOption('disabled')) {
				// Very strange situation,
				// somebody probably tried to do something nasty.
				return redirect()->back()->withInput();
			}
		} else {
			$validations['identity_number'] = $validator;
		}


		$form->validate($validations);

		if (!$form->isValid()) {
			Log::notice('Sing up form invalid, redirecting...');

			return redirect()->back()->withErrors($form->getErrors())->withInput();
		}

		$user = Auth::user();

		$this->updateAccount($user, $request, $form);

		if (!!Session::get('orderId')) {
			$this->updateOrder($user, $request);
		} else {
			$this->createOrder($user, $request);
		}

		return redirect(route('payment-confirm-order'));
	}

	protected function setupForm() {
		$user = Auth::user();
		$form = $this->form(PersonalDataForm::class, [
			'method' => 'POST',
			'url'    => route('payment-personal-data-post'),
			'model'  => $user,
		]);

		if ($user && $user->orders()->where(['paid' => 1])->exists()) {
			if ($user->first_name) {
				$form->getField('first_name')->disable()->setOption('rules', '');
			}
			if ($user->last_name) {
				$form->getField('last_name')->disable()->setOption('rules', '');
			}

			$personalData = $user->personalData->first();
			if ($personalData && ($personalData->personal_identity_number || $personalData->identity_card_number || $personalData->passport_number)) {
				$form->getField('identity_number_type')->disable();
				$form->getField('identity_number')->disable()->setOption('rules', '');
			}
		}

		return $form;
	}

	protected function createOrder($user, $request)
	{
		\Log::notice('Creating order');
		$order = $user->orders()->create([
			'product_id' => Session::get('product')->id,
			'session_id' => str_random(32),
			'invoice'    => $request->invoice ?? $user->invoice ?? 0,
		]);
		Session::put('orderId', $order->id);

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
		$coupon = new Coupon([
			'name'         => 'Study Buddy',
			'type'         => 'amount',
			'value'        => 100,
			'expires_at'   => $expires,
			'code'         => strtoupper(str_random(7)),
			'times_usable' => 0,
		]);

		$order->studyBuddy()->create([
			'code' => $coupon->code,
		]);

		$coupon->save();
		$coupon->products()->attach(
			Product::whereIn('slug', ['wnl-online', 'wnl-online-onsite'])->get()
		);
	}

	protected function updateAccount($user, $request, $form)
	{
		$userData = [
			'first_name' => $request->first_name ?? $user->first_name,
			'last_name' => $request->last_name ?? $user->last_name,
			'invoice_name' => $request->invoice_name ?? $user->invoice_name,
			'invoice_nip' => $request->invoice_nip ?? $user->invoice_nip,
			'invoice_address' => $request->invoice_address ?? $user->invoice_address,
			'invoice_zip' => $request->invoice_zip ?? $user->invoice_zip,
			'invoice_city' => $request->invoice_city ?? $user->invoice_city,
			'invoice_country' => $request->invoice_country ?? $user->invoice_country,
		];

		if (!$form->getField('first_name')->getOption('disabled')) {
			$userData['first_name'] = $request->first_name ?? $user->first_name;
		}

		if (!$form->getField('last_name')->getOption('disabled')) {
			$userData['last_name'] = $request->last_name ?? $user->last_name;
		}

		$user->update($userData);

		$user->profile()->updateOrCreate(
			['user_id' => $user->id],
			[
				'first_name' => $user->first_name,
				'last_name'  => $user->last_name,
			]
		);

		$user->billing()->updateOrCreate(
			['user_id' => $user->id],
			[
				'company_name' => $user->invoice_name,
				'vat_id'       => $user->invoice_nip,
				'address'      => $user->invoice_address,
				'zip'          => $user->invoice_zip,
				'city'         => $user->invoice_city,
				'country'      => $user->invoice_country,
			]
		);

		$user->userAddress()->updateOrCreate(
		['user_id' => $user->id],
		[
			'street'    => $request->get('address'),
			'zip'       => $request->get('zip'),
			'city'      => $request->get('city'),
			'phone'     => $request->get('phone'),
			'recipient' => $request->get('recipient'),
		]);

		if (!$form->getField('identity_number')->getOption('disabled')) {
			$user->personalData()->updateOrCreate(
				['user_id' => $user->id],
				$this->getIdentityNumbersArray($request)
			);
		}
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
