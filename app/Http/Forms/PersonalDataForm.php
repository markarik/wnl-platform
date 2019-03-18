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
			->add('identity_number_type', 'choice', [
				'choices' => [
					'personal_identity_number' => trans('payment.identity_number_personal_identity_number'),
					'passport_number' => trans('payment.identity_number_passport_number'),
				],
				'expanded' => true,
				'selected' => ['personal_identity_number'],
				'multiple' => false,
				'rules' => $identityNumberDisabled ? '' : 'required|in:personal_identity_number,passport_number',
				'choice_options' => [
					'attr' => [
						'disabled' => $identityNumberDisabled
					],
				],
			])
			->add('identity_number', 'text', [
				'label' => trans('payment.identity_number'),
				'rules' => $identityNumberDisabled ? '' : 'required',
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
				'attr' => [
					'class' => 'a-checkbox',
				]
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

	public function validate($validationRules = [], $messages = [])
	{
		$validator = $this->getIdentityNumberValidator($this->getRequest()->get('identity_number_type'));

		if ($validator && !$this->getField('identity_number')->getOption('attr.disabled')) {
			$validationRules['identity_number'] = $validator;
		}

		return parent::validate($validationRules, $messages);
	}

	protected function getIdentityNumberValidator($identityNumberType) {
		$validators = [
			'passport_number' => new ValidatePassportNumber,
			'personal_identity_number' => new ValidatePersonalIdentityNumber,
		];

		// If someone sends invalid `identity_number_type` then disable `identity_number` validator
		// Request will fail anyway
		if (!array_key_exists($identityNumberType, $validators)) {
			return false;
		}

		return $validators[$identityNumberType];
	}
}
