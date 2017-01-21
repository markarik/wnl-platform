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
				'attr' => [
					'placeholder' => trans('payment.email'),
				],
			])
			->add('password', 'password', [
				'label' => trans('payment.password'),
				'rules' => 'required|confirmed|min:6',
				'attr' => [
					'placeholder' => trans('payment.password'),
				],
			])
			->add('password_confirmation', 'password', [
				'label' => trans('payment.password-confirm'),
				'rules' => 'required',
				'attr' => [
					'placeholder' => trans('payment.password-confirm'),
				],
			])

			// Personal data

            ->add('first_name', 'text', [
				'label' => trans('payment.first-name'),
                'rules' => 'required',
                'attr' => [
                    'placeholder' => trans('payment.invoice_name'),
                ],
            ])
            ->add('last_name', 'text', [
				'label' => trans('payment.last-name'),
                'rules' => 'required',
                'attr' => [
                    'placeholder' => trans('payment.last-name'),
                ],
            ])
            ->add('address', 'text', [
				'label' => trans('payment.address'),
                'rules' => 'required',
                'attr' => [
                    'placeholder' => trans('payment.address'),
                ],
            ])
            ->add('zip', 'text', [
				'label' => trans('payment.zip'),
                'rules' => 'required',
                'attr' => [
                    'placeholder' => trans('payment.zip'),
                ],
            ])
            ->add('city', 'text', [
				'label' => trans('payment.city'),
                'rules' => 'required',
                'attr' => [
                    'placeholder' => trans('payment.city'),
                ],
            ])

			// Invoice

			->add('invoice', 'checkbox', [
				'label' => trans('payment.invoice')
			])
			->add('invoice_name', 'text', [
				'label' => trans('payment.invoice_name'),
				'attr' => [
					'placeholder' => trans('payment.invoice_name'),
				],
			])
			->add('invoice_nip', 'text', [
				'label' => trans('payment.invoice_nip'),
				'attr' => [
					'placeholder' => trans('payment.invoice_nip'),
				],
			])
			->add('invoice_address', 'text', [
				'label' => trans('payment.invoice_address'),
				'rules' => 'required',
				'attr' => [
					'placeholder' => trans('payment.invoice_address'),
				],
			])
			->add('invoice_zip', 'text', [
				'label' => trans('payment.invoice_zip'),
				'rules' => 'required',
				'attr' => [
					'placeholder' => trans('payment.invoice_zip'),
				],
			])
			->add('invoice_city', 'text', [
				'label' => trans('payment.invoice_city'),
				'rules' => 'required',
				'attr' => [
					'placeholder' => trans('payment.invoice_city'),
				],
			])
			->add('invoice_country', 'text', [
				'label' => trans('payment.invoice_country'),
				'rules' => 'required',
				'attr' => [
					'placeholder' => trans('payment.invoice_country'),
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
			]);
    }
}
