<?php

namespace App\Http\Controllers\Payment;

use App\Http\Forms\SignUpForm;
use App\Mail\UserSignedUp;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class StepTwoContoller extends Controller
{
	use FormBuilderTrait;

	public function index(FormBuilder $formBuilder)
	{
		$form = $this->form(SignUpForm::class, [
			'method' => 'POST',
			'url'    => url('/payment/step2'),
			'model'  => Auth::user(),
		]);

		return view('payment.step2', [
			'form' => $form,
		]);

	}

	public function handle(Request $request)
	{
		$form = $this->form(SignUpForm::class);

		if (Auth::check()) {
			$form->validate(['email' => 'required|email']);
		}

		if (!$form->isValid()) {
			return redirect()->back()->withErrors($form->getErrors())->withInput();
		}

		$user = User::updateOrCreate(
			['email' => $request->get('email')],
			[
				'first_name'         => $request->get('first_name'),
				'last_name'          => $request->get('last_name'),
				'address'            => $request->get('address'),
				'zip'                => $request->get('zip'),
				'city'               => $request->get('city'),
				'email'              => $request->get('email'),
				'phone'              => $request->get('phone'),
				'password'           => $request->get('password'),
				'invoice'            => $request->get('invoice') ?? 0,
				'invoice_name'       => $request->get('invoice_name'),
				'invoice_nip'        => $request->get('invoice_nip'),
				'invoice_address'    => $request->get('invoice_address'),
				'invoice_zip'        => $request->get('invoice_zip'),
				'invoice_city'       => $request->get('invoice_city'),
				'invoice_country'    => $request->get('invoice_country'),
				'consent_newsletter' => $request->get('consent_newsletter') ?? 0,
			]
		);
		$user->orders()->create([
			'product_id' => 1,
			'session_id' => str_random(32),
		]);

		Auth::login($user);

		Mail::to(Auth::user())->send(new UserSignedUp);

		return redirect(url('/payment/step3'));
	}
}
