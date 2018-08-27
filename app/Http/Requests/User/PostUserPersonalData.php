<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidatePersonalIdentityNumber;
use App\Rules\ValidatePassportNumber;
use App\Rules\ValidateIdentityCardNumber;

class PostUserPersonalData extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		$user = User::fetch($this->route('userId'));

		return $this->user()->can('update', $user);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$identityType = $this->request->get('identity_type');

		if (!$identityType) return false;
		
		if ($identityType === 'personal_identity_number') {
			return [
				'personal_identity_number' => [
					'required',
					'digits:11',
					'string',
					new ValidatePersonalIdentityNumber
				]
			];
		} else if ($identityType === 'identity_card') {
			return [
				'personal_identity_number' => [
					'required',
					'string',
					'size:9',
					new ValidateIdentityCardNumber
				]
			];
		} else if ($identityType === 'passport') {
			return [
				'personal_identity_number' => [
					'required',
					'string',
					'size:9',
					new ValidatePassportNumber
				]
			];
		}
	}
}
