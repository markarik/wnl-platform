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
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.email'),
				],
			])
			->add('password', 'password', [
				'label' => trans('payment.password'),
				'rules' => 'required|confirmed|min:6',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.password'),
				],
			])
			->add('password_confirmation', 'password', [
				'label' => trans('payment.password-confirm'),
				'rules' => 'required',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.password-confirm'),
				],
			])

			// Identity number
			->add('identity_number_type', 'choice', [
				'choices' => [
					'personal_identity_number' => trans('payment.identity_number_personal_identity_number'),
					'passport_number' => trans('payment.identity_number_passport_number'),
				],
				'expanded' => true,
				'selected' => ['personal_identity_number'],
				'multiple' => false,
			])
			->add('identity_number', 'text', [
				'label' => trans('payment.identity_number'),
				'rules' => 'required',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.identity_number'),
				],
			])
			->add('first_name', 'text', [
				'label' => trans('payment.first-name'),
				'rules' => 'required',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.first-name'),
				],
			])
			->add('last_name', 'text', [
				'label' => trans('payment.last-name'),
				'rules' => 'required',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.last-name'),
				],
			]);

			if (empty($coupon) || $coupon->kind !== Coupon::KIND_PARTICIPANT) {
				// Personal data
				$this->add('recipient', 'text', [
					'label' => trans('payment.recipient'),
					'rules' => 'required',
					'attr'  => [
						'class' => 'input',
						'placeholder' => trans('payment.recipient'),
					],
				])
				->add('address', 'text', [
					'label' => trans('payment.address'),
					'rules' => 'required',
					'attr'  => [
						'class' => 'input',
						'placeholder' => trans('payment.address'),
					],
				])
				->add('zip', 'text', [
					'label' => trans('payment.zip'),
					'rules' => 'required',
					'attr'  => [
						'class' => 'input',
						'placeholder' => trans('payment.zip'),
					],
				])
				->add('city', 'text', [
					'label' => trans('payment.city'),
					'rules' => 'required',
					'attr'  => [
						'class' => 'input',
						'placeholder' => trans('payment.city'),
					],
				])
				->add('phone', 'text', [
					'label' => trans('payment.phone'),
					'rules' => 'required',
					'attr'  => [
						'class' => 'input',
						'placeholder' => trans('payment.phone'),
					],
				]);
			}

			// Invoice
			$this->add('invoice', 'checkbox', [
				'label' => trans('payment.invoice'),
				'attr' => [
					'class' => 'checkbox',
				]
			])
			->add('invoice_name', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-name'),
				'rules' => 'required_with:invoice',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.invoice-name'),
				],
			])
			->add('invoice_nip', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-nip'),
				'rules' => 'required_with:invoice',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.invoice-nip'),
				],
			])
			->add('invoice_address', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-address'),
				'rules' => 'required_with:invoice',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.invoice-address'),
				],
			])
			->add('invoice_zip', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-zip'),
				'rules' => 'required_with:invoice',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.invoice-zip'),
				],
			])
			->add('invoice_city', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-city'),
				'rules' => 'required_with:invoice',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.invoice-city'),
				],
			])
			->add('invoice_country', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-country'),
				'rules' => 'required_with:invoice',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.invoice-country'),
				],
			])
			// Consents
			->add('consent_terms', 'checkbox', [
				'label' => trans('payment.personal-data-tou-content', [
					'tou-link-content' => trans('payment.personal-data-tou-link-content'),
					'tou-link-href' => trans('payment.tou-link-href'),
					'privacy-link-content' => trans('payment.personal-data-privacy-link-content'),
					'privacy-link-href' => trans('payment.privacy-link-href'),
				]),
				'rules' => 'required',
				'attr' => [
					'class' => 'checkbox',
				]
			])
			->add('consent_newsletter', 'checkbox', [
				'label' => trans('payment.personal-data-consent-newsletter'),
				'attr' => [
					'class' => 'checkbox',
				]
			]);
	}
}
