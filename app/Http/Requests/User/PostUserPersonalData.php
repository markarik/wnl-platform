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
		if ($this->request->get('identity_type') == 'personal_identity_number') {
			return [
				'personal_identity_number' => [
					'required',
					'digits:11',
					'string',
					new ValidatePersonalIdentityNumber
				]
			];
		} else if ($this->request->get('identity_type') == 'identity_card') {
			return [
				'personal_identity_number' => [
					'required',
					'string',
					'size:9',
					new ValidateIdentityCardNumber
				]
			];
		} else if ($this->request->get('identity_type') == 'passport') {
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
