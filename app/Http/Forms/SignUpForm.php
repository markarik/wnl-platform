<?php

namespace App\Http\Forms;

use Kris\LaravelFormBuilder\Form;

class SignUpForm extends Form
{
	public function buildForm() {
		$this
			->add('first_name', 'text', [
				'rules' => 'required',
			])
			->add('last_name', 'text', [
				'rules' => 'required',
			])
			->add('address', 'text', [
				'rules' => 'required',
			])
			->add('zip', 'text', [
				'rules' => 'required',
			])
			->add('city', 'text', [
				'rules' => 'required',
			])
			->add('email', 'email', [
				'rules' => 'required|email',
			])
			->add('privacy_policy', 'checkbox', [
				'rules' => 'required',
			])
			->add('newsletter', 'checkbox', [
				'rules' => 'required'
			]);
	}
}
