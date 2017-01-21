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
					'placeholder' => trans('payment.email'),
				],
			])
			->add('phone', 'text', [
				'label' => trans('payment.phone'),
				'rules' => 'required',
				'attr'  => [
					'placeholder' => trans('payment.phone'),
				],
			])
			->add('password', 'password', [
				'label' => trans('payment.password'),
				'rules' => 'required|confirmed|min:6',
				'attr'  => [
					'placeholder' => trans('payment.password'),
				],
			])
			->add('password_confirmation', 'password', [
				'label' => trans('payment.password-confirm'),
				'rules' => 'required',
				'attr'  => [
					'placeholder' => trans('payment.password-confirm'),
				],
			])
			// Personal data

			->add('first_name', 'text', [
				'label' => trans('payment.first-name'),
				'rules' => 'required',
				'attr'  => [
					'placeholder' => trans('payment.invoice-name'),
				],
			])
			->add('last_name', 'text', [
				'label' => trans('payment.last-name'),
				'rules' => 'required',
				'attr'  => [
					'placeholder' => trans('payment.last-name'),
				],
			])
			->add('address', 'text', [
				'label' => trans('payment.address'),
				'rules' => 'required',
				'attr'  => [
					'placeholder' => trans('payment.address'),
				],
			])
			->add('zip', 'text', [
				'label' => trans('payment.zip'),
				'rules' => 'required',
				'attr'  => [
					'placeholder' => trans('payment.zip'),
				],
			])
			->add('city', 'text', [
				'label' => trans('payment.city'),
				'rules' => 'required',
				'attr'  => [
					'placeholder' => trans('payment.city'),
				],
			])
			// Invoice


			->add('invoice', 'checkbox', [
				'label' => trans('payment.invoice'),
				'checked' => true
			])
			->add('invoice_name', 'text', [
				'label' => trans('payment.invoice-name'),
				'rules' => 'required_if:invoice,1',
				'attr'  => [
					'placeholder' => trans('payment.invoice-name'),
				],
			])
			->add('invoice_nip', 'text', [
				'label' => trans('payment.invoice-nip'),
				'rules' => 'required_if:invoice,1',
				'attr'  => [
					'placeholder' => trans('payment.invoice-nip'),
				],
			])
			->add('invoice_address', 'text', [
				'label' => trans('payment.invoice-address'),
				'rules' => 'required_if:invoice,1',
				'attr'  => [
					'placeholder' => trans('payment.invoice-address'),
				],
			])
			->add('invoice_zip', 'text', [
				'label' => trans('payment.invoice-zip'),
				'rules' => 'required_if:invoice,1',
				'attr'  => [
					'placeholder' => trans('payment.invoice-zip'),
				],
			])
			->add('invoice_city', 'text', [
				'label' => trans('payment.invoice-city'),
				'rules' => 'required_if:invoice,1',
				'attr'  => [
					'placeholder' => trans('payment.invoice-city'),
				],
			])
			->add('invoice_country', 'text', [
				'label' => trans('payment.invoice-country'),
				'rules' => 'required_if:invoice,1',
				'attr'  => [
					'placeholder' => trans('payment.invoice-country'),
				],
			])
			// Consents

			->add('consent_order', 'checkbox', [
				'label' => trans('payment.personal-data-consent-order'),
				'rules' => 'required',
			])
			->add('consent_account', 'checkbox', [
				'label' => trans('payment.personal-data-consent-account'),
				'rules' => 'required',
			])
			->add('consent_newsletter', 'checkbox', [
				'label' => trans('payment.personal-data-consent-newsletter'),
			])
			->add('consent_terms', 'checkbox', [
				'label' => trans('payment.terms-of-use-content'),
				'rules' => 'required',
			]);
	}
}
