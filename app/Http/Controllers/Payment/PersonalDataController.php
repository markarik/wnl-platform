<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Forms\AddressForm;
use App\Http\Forms\PersonalDataForm;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use App\Traits\CheckoutTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Kris\LaravelFormBuilder\FormBuilderTrait;

class PersonalDataController extends Controller
{
	use CheckoutTrait;
	use FormBuilderTrait;

	public function index(Request $request)
	{
		$product = $this->getProduct($request);

		if (
			!$product instanceof Product ||
			!$product->available ||
			$product->signups_close->isPast() ||
			$product->signups_start->isFuture()
		) {
			return view('payment.signups-closed', ['product' => $product]);
		}

		$user = Auth::user();
		$coupon = $this->readCoupon($product, $user);
		$form = $this->setupForm($coupon, $user);

		$productPriceWithCoupon = null;

//		TODO fix updating the form
		return view('payment.personal-data', [
			'form'    => $form,
			'product' => $product,
			'productPriceWithCoupon' => $product->getPriceWithCoupon($coupon),
			'coupon' => $coupon
		]);
	}

	public function handle(Request $request)
	{
		$product = $this->getProduct($request);
		$user = Auth::user();
		$coupon = $this->readCoupon($product, $user);
		$form = $this->setupForm($coupon, $user);

		if (!$form->isValid()) {
			Log::notice('Sing up form invalid, redirecting...');

			return redirect()->back()->withErrors($form->getErrors())->withInput();
		}

		$this->updateAccount($user, $request, $form);

		if (!!Session::get('orderId')) {
			$this->updateOrder($product, $user, $request);
		} else {
			$this->createOrder($product, $user, $request);
		}

		return redirect(route('payment-confirm-order'));
	}

	protected function setupForm($coupon, $user) {
		$form = $this->form(PersonalDataForm::class, [
			'method' => 'POST',
			'url'    => route('payment-personal-data-post'),
			'model'  => $user,
		]);

		if (empty($coupon) || $coupon->kind !== Coupon::KIND_PARTICIPANT) {
			$form->compose(AddressForm::class);
		}

		return $form;
	}

	protected function createOrder(Product $product, User $user, Request $request)
	{
		\Log::notice('Creating order');
		$order = $user->orders()->create([
			'product_id' => Session::get('productId'),
			'session_id' => str_random(32),
			'invoice'    => $request->invoice ?? $user->invoice ?? 0,
		]);

		Session::put('orderId', $order->id);

		$coupon = $this->readCoupon($product, $user);

		if (!empty($coupon)) {
			$this->addCoupon($order, $coupon);
		} else if ($order->product->slug !== Product::SLUG_WNL_ALBUM) {
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
			Product::whereIn('slug', [Product::SLUG_WNL_ONLINE])->get()
		);
	}

	protected function updateAccount($user, $request, $form)
	{
		$userData = [
			'invoice' => (bool) $request->invoice,
			'invoice_name' => $request->invoice_name ?? $user->invoice_name,
			'invoice_nip' => $request->invoice_nip ?? $user->invoice_nip,
			'invoice_address' => $request->invoice_address ?? $user->invoice_address,
			'invoice_zip' => $request->invoice_zip ?? $user->invoice_zip,
			'invoice_city' => $request->invoice_city ?? $user->invoice_city,
			'invoice_country' => $request->invoice_country ?? $user->invoice_country,
		];

		if (!$form->first_name->getOption('attr.disabled')) {
			$userData['first_name'] = $request->first_name ?? $user->first_name;
		}

		if (!$form->last_name->getOption('attr.disabled')) {
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

		if (!$form->personal_identity_number->getOption('attr.disabled')) {
			$user->personalData()->updateOrCreate(
				['user_id' => $user->id],
				[
					'passport_number' => $request->get('passport_number'),
					'personal_identity_number' => $request->get('personal_identity_number'),
				]
			);
		}
	}

	protected function updateOrder(Product $product, User $user, Request $request)
	{
		Log::notice('Updating order');
		$user->orders()
			->recent()
			->update([
				'product_id' => $product->id,
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
