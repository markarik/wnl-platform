<?php

namespace App\Http\Forms;

use Kris\LaravelFormBuilder\Form;

class SignUpForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('first_name', 'text', [
                'rules' => 'required',
                'attr' => [
                    'placeholder' => trans('payment.first-name'),
                ],
            ])
            ->add('last_name', 'text', [
                'rules' => 'required',
                'attr' => [
                    'placeholder' => trans('payment.last-name'),
                ],
            ])
            ->add('address', 'text', [
                'rules' => 'required',
                'attr' => [
                    'placeholder' => trans('payment.address'),
                ],
            ])
            ->add('zip', 'text', [
                'rules' => 'required',
                'attr' => [
                    'placeholder' => trans('payment.zip'),
                ],
            ])
            ->add('city', 'text', [
                'rules' => 'required',
                'attr' => [
                    'placeholder' => trans('payment.city'),
                ],
            ])
            ->add('email', 'email', [
                'rules' => 'required|email|unique:users,email',
                'attr' => [
                    'placeholder' => trans('payment.email'),
                ],
            ])
            ->add('password', 'password', [
                'rules' => 'required|confirmed|min:6',
                'attr' => [
                    'placeholder' => trans('payment.password'),
                ],
            ])
            ->add('password_confirmation', 'password', [
                'rules' => 'required',
                'attr' => [
                    'placeholder' => trans('payment.password_confirm'),
                ],
            ])
            ->add('privacy_policy', 'checkbox', [
                'rules' => 'required',
            ])
            ->add('newsletter', 'checkbox');
    }
}
