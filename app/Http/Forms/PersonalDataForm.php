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
		$personalData = $userHasPaidOrder ? $user->personalData : null;

		$identityNumberDisabled = $personalData &&
			($personalData->personal_identity_number || $personalData->identity_card_number || $personalData->passport_number);

		$this
			// Identity number
			->add('passport_number', 'text', [
				'label' => trans('payment.passport_number'),
				'rules' => $identityNumberDisabled ? '' : ['required_with:no_identity_number', new ValidatePassportNumber],
				'attr'  => [
					'disabled' => $identityNumberDisabled
				],
			])
			->add('personal_identity_number', 'text', [
				'label' => trans('payment.personal_identity_number'),
				'rules' => $identityNumberDisabled ? '' : ['required_without:no_identity_number', new ValidatePersonalIdentityNumber],
				'attr'  => [
					'disabled' => $identityNumberDisabled
				],
			])
			->add('no_identity_number', 'checkbox', [
				'label' => trans('payment.no_identity_number'),
				'attr'  => [
					'disabled' => $identityNumberDisabled
				],
			])

			// Personal data
			->add('first_name', 'text', [
				'label' => trans('payment.first-name'),
				'rules' => $firstNameDisabled ? '' : 'required',
				'attr'  => [
					'disabled' => $firstNameDisabled,
				],
			])
			->add('last_name', 'text', [
				'label' => trans('payment.last-name'),
				'rules' => $lastNameDisabled ? '' : 'required',
				'attr'  => [
					'disabled' => $lastNameDisabled,
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
			])
			->add('invoice_nip', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-nip'),
				'rules' => 'required_with:invoice',
			])
			->add('invoice_address', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-address'),
				'rules' => 'required_with:invoice',
			])
			->add('invoice_zip', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-zip'),
				'rules' => 'required_with:invoice',
			])
			->add('invoice_city', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-city'),
				'rules' => 'required_with:invoice',
			])
			->add('invoice_country', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-country'),
				'rules' => 'required_with:invoice',
			]);
	}
}
