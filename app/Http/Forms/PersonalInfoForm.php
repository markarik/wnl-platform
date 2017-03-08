<?php

namespace App\Http\Forms;

use Kris\LaravelFormBuilder\Form;

class PersonalInfoForm extends Form
{
	// TODO: Mar 7, 2017 - Move messages from the payment file to a form file
	public function buildForm()
	{
		$this
			// Personal data form
			->add('first_name', 'text', [
				'label' => trans('payment.first-name'),
				'rules' => 'required',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.invoice-name'),
				],
			])
			->add('last_name', 'text', [
				'label' => trans('payment.last-name'),
				'rules' => 'required',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.last-name'),
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
			]);
	}
}
