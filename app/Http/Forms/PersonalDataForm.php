<?php

namespace App\Http\Forms;

use Kris\LaravelFormBuilder\Form;
use App\Rules\ValidatePassportNumber;
use App\Rules\ValidatePersonalIdentityNumber;

class PersonalDataForm extends Form
{
	public function buildForm()
	{
		$user = $this->getModel();
		$userHasPaidOrder = $user && $user->orders()->where(['paid' => 1])->exists();
		$firstNameDisabled = $userHasPaidOrder && $user->first_name;
		$lastNameDisabled = $userHasPaidOrder && $user->last_name;
		$identityNumberDisabled = $userHasPaidOrder && ($user->personal_identity_number || $user->passport_number);

		$this
			->add('passport_number', 'text', [
				'label' => trans('payment.passport-number'),
				'rules' => $identityNumberDisabled ? '' : ['required_with:no_identity_number', new ValidatePassportNumber],
				'attr'  => [
					'disabled' => $identityNumberDisabled,
					'placeholder' => trans('payment.passport-number-placeholder'),
				],
				'error_messages' => [
					'passport_number.required_with' => trans('payment.passport-number-required')
				],
			])
			->add('personal_identity_number', 'text', [
				'label' => trans('payment.personal-identity-number'),
				'rules' => $identityNumberDisabled ? '' : ['required_without:no_identity_number', new ValidatePersonalIdentityNumber],
				'attr'  => [
					'disabled' => $identityNumberDisabled,
					'placeholder' => trans('payment.personal-identity-number-placeholder'),
				],
				'error_messages' => [
					'personal_identity_number.required_without' => trans('validation.required')
				],
			])
			->add('no_identity_number', 'checkbox', [
				'label' => trans('payment.no-identity-number'),
				'attr'  => [
					'disabled' => $identityNumberDisabled,
				],
				'checked' => !empty($user->passport_number),
			])

			// Personal data
			->add('first_name', 'text', [
				'label' => trans('payment.first-name'),
				'rules' => $firstNameDisabled ? '' : 'required',
				'attr'  => [
					'disabled' => $firstNameDisabled,
					'placeholder' => trans('payment.first-name-placeholder'),
				],
			])
			->add('last_name', 'text', [
				'label' => trans('payment.last-name'),
				'rules' => $lastNameDisabled ? '' : 'required',
				'attr'  => [
					'disabled' => $lastNameDisabled,
					'placeholder' => trans('payment.last-name-placeholder'),
				],
			])

			// Invoice

			->add('invoice', 'checkbox', [
				'label' => trans('payment.invoice'),
			])
			->add('invoice_name', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-name'),
				'rules' => 'required_with:invoice',
				'attr' => [
					'placeholder' => trans('payment.invoice-name'),
				],
			])
			->add('invoice_address', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-address'),
				'rules' => 'required_with:invoice',
				'attr' => [
					'placeholder' => trans('payment.invoice-address'),
				],
			])
			->add('invoice_zip', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-zip'),
				'rules' => 'required_with:invoice',
				'attr' => [
					'placeholder' => trans('payment.invoice-zip-placeholder'),
				],
			])
			->add('invoice_city', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-city'),
				'rules' => 'required_with:invoice',
				'attr' => [
					'placeholder' => trans('payment.invoice-city'),
				],
			])
			->add('invoice_country', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-country'),
				'rules' => 'required_with:invoice',
				'attr' => [
					'placeholder' => trans('payment.invoice-country'),
				],
			])
			->add('invoice_nip', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-nip'),
				'rules' => 'required_with:invoice',
				'attr' => [
					'placeholder' => trans('payment.invoice-nip'),
				],
			]);
	}
}
