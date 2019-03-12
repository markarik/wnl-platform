<?php

namespace App\Http\Forms;

use Kris\LaravelFormBuilder\Form;

class SignUpForm extends Form
{
	public function buildForm()
	{
		$this
			->add('email', 'email', [
				'label' => trans('payment.email'),
				'rules' => 'required|email|unique:users,email',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.email'),
				],
			])
			->add('password', 'password', [
				'label' => trans('payment.password'),
				'rules' => 'required|min:6',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.password'),
				],
			]);
	}
}
