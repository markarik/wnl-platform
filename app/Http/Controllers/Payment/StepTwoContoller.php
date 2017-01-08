<?php

namespace App\Http\Controllers\Payment;

use App\Http\Forms\SignUpForm;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Kris\LaravelFormBuilder\FormBuilder;

class StepTwoContoller extends Controller
{
	public function index(FormBuilder $formBuilder) {

		$form = $formBuilder->create(SignUpForm::class, [
			'method' => 'POST',
			'url'    => url('/payment/step2'),
		]);

		return view('payment.step2', [
			'form' => $form,
		]);

	}

	public function handle() {

	}
}
