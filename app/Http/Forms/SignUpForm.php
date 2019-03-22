<?php

namespace App\Http\Forms;

use App\Models\Coupon;
use Kris\LaravelFormBuilder\Form;

class SignUpForm extends Form
{
	public function buildForm()
	{
		$coupon = $this->getData('coupon');
		$this
			->add('email', 'email', [
				'label' => trans('payment.email'),
				'rules' => 'required|email|unique:users,email',
			])
			->add('password', 'password', [
				'label' => trans('payment.password'),
				'rules' => 'required|min:6',
			]);
	}
}
