<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfile extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		$user = User::fetch($this->route('id'));

		return $this->user()->can('update', $user->profile);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'first_name'   => 'required|alpha_spaces|max:20',
			'last_name'    => 'required|alpha_spaces|max:20',
			'public_email' => 'email|nullable|max:50',
			'public_phone' => 'nullable|max:20',
			'username'     => 'max:30|alpha_num',
		];
	}
}
