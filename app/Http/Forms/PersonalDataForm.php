<?php

namespace App\Http\Forms;

use Kris\LaravelFormBuilder\Form;
use Illuminate\Contracts\Validation\Validator;
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
			->add('phone', 'text', [
				'label' => trans('payment.phone'),
				'rules' => 'required',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.phone'),
				],
			])

			// Identity number
			->add('identity_number_type', 'choice', [
				'choices' => [
					'personal_identity_number' => trans('payment.identity_number_personal_identity_number'),
					'passport_number' => trans('payment.identity_number_passport_number'),
				],
				'expanded' => true,
				'selected' => ['personal_identity_number'],
				'multiple' => false,
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
					'class' => 'input',
					'placeholder' => trans('payment.identity_number'),
					'disabled' => $identityNumberDisabled
				],
			])

			// Personal data
			->add('first_name', 'text', [
				'label' => trans('payment.first-name'),
				'rules' => $firstNameDisabled ? '' : 'required',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.first-name'),
					'disabled' => $firstNameDisabled,
				],
			])
			->add('last_name', 'text', [
				'label' => trans('payment.last-name'),
				'rules' => $lastNameDisabled ? '' : 'required',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.last-name'),
					'disabled' => $lastNameDisabled,
				],
			])
			->add('recipient', 'text', [
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

			// Invoice

			->add('invoice', 'checkbox', [
				'label' => trans('payment.invoice'),
				'attr' => [
					'class' => 'checkbox',
				]
			])
			->add('invoice_name', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-name'),
				'rules' => 'required_with:invoice',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.invoice-name'),
				],
			])
			->add('invoice_nip', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-nip'),
				'rules' => 'required_with:invoice',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.invoice-nip'),
				],
			])
			->add('invoice_address', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-address'),
				'rules' => 'required_with:invoice',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.invoice-address'),
				],
			])
			->add('invoice_zip', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-zip'),
				'rules' => 'required_with:invoice',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.invoice-zip'),
				],
			])
			->add('invoice_city', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-city'),
				'rules' => 'required_with:invoice',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.invoice-city'),
				],
			])
			->add('invoice_country', 'text', [
				'error_messages' => [
					'required_with' => trans('payment.invoice-required')
				],
				'label' => trans('payment.invoice-country'),
				'rules' => 'required_with:invoice',
				'attr'  => [
					'class' => 'input',
					'placeholder' => trans('payment.invoice-country'),
				],
			]);
	}


	/**
	 * Validate the form.
	 *
	 * @param array $validationRules
	 * @param array $messages
	 * @return Validator
	 */
	public function validate($validationRules = [], $messages = [])
	{
		$validator = $this->getIdentityNumberValidator($this->getRequest()->get('identity_number_type'));
		if (!is_object($validator)) {
			if (!$this->identity_number->getOption('attr.disabled')) {
				// Very strange situation,
				// somebody probably tried to do something nasty.
				return redirect()->back()->withInput();
			}
		} else {
			$validationRules['identity_number'] = $validator;
		}

		return parent::validate($validationRules, $messages);
	}

	protected function getIdentityNumberValidator($identityNumberType) {
		$validators = [
			'passport_number' => new ValidatePassportNumber,
			'personal_identity_number' => new ValidatePersonalIdentityNumber,
		];

		if (!array_key_exists($identityNumberType, $validators)) {
			return false;
		}

		return $validators[$identityNumberType];
	}
}
