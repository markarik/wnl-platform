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
		])
		->add('address', 'text', [
			'label' => trans('payment.address'),
			'rules' => 'required',
		])
		->add('zip', 'text', [
			'label' => trans('payment.zip'),
			'rules' => 'required',
		])
		->add('city', 'text', [
			'label' => trans('payment.city'),
			'rules' => 'required',
		])
		->add('phone', 'text', [
			'label' => trans('payment.phone'),
			'rules' => 'required',
		]);
	}
}
