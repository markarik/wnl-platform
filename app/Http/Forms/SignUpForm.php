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
            ->add('first_name', 'text', [
				'label' => trans('payment.first-name'),
                'rules' => 'required',
                'attr' => [
                    'placeholder' => trans('payment.first-name'),
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
