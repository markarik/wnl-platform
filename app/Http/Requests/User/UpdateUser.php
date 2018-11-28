<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUser extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return Auth::user()->isAdmin();
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$user = User::fetch($this->route('id'));

		return [
			'first_name' => 'string|required',
			'last_name' => 'string|required',
			'email' => ['email',  Rule::unique('users')->ignore($user->id)],
			'password' => 'min:8',
			'roles' => 'array'
		];
	}
}
