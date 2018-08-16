<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserIdentity extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		$user = User::fetch($this->route('id'));

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
				'personal_identity_number' => 'required|digits:11'
			];
		} else if ($this->request->get('identity_type') == 'identity_card' || $this->request->get('identity_type') == 'passport') {
			return [
				'personal_identity_number' => 'required|string'
			];
		}
	}
}
