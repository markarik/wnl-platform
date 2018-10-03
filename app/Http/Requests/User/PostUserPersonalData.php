<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidatePersonalIdentityNumber;
use App\Rules\ValidatePassportNumber;

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
		return [
			'personal_identity_number' => [
				'nullable',
				'digits:11',
				'string',
				new ValidatePersonalIdentityNumber
			],
			'passport_number' => [
				'nullable',
				'string',
				'size:9',
				new ValidatePassportNumber
			]
		];
	}
}
