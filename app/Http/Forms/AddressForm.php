<?php

namespace App\Http\Forms;

use Kris\LaravelFormBuilder\Form;

class AddressForm extends Form
{
	public function buildForm()
	{
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
}
