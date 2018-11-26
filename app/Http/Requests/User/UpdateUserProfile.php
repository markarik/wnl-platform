<?php

namespace App\Http\Requests\User;

use App\Models\User;
use App\Models\UserProfile;
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
			'public_email'      => 'email|nullable|max:50',
			'public_phone'      => 'nullable|max:20',
			'username'          => 'nullable|max:30|alpha_num',
			'display_name'      => 'nullable|alpha_comas|max:100',
			'city'              => 'nullable|max:50|alpha_comas',
			'university'        => 'nullable|max:200|alpha_comas',
			'specialization'    => 'nullable|max:400|alpha_comas',
			'help'              => 'nullable|max:400|alpha_comas',
			'interests'         => 'nullable|max:400|alpha_comas',
			'about'             => 'nullable|max:400|alpha_comas',
			'learning_location' => 'nullable|max:50|alpha_comas',
		];
	}

	/**
	 * Configure the validator instance.
	 *
	 * @param  \Illuminate\Validation\Validator $validator
	 *
	 * @return void
	 */
	public function withValidator($validator)
	{
		$validator->after(function ($validator) {
			if ($this->usernameIsInvalid()) {
				$validator->errors()->add('username', trans('validation.unique'));
			}
		});
	}

	private function usernameIsInvalid()
	{
		if ($this->request->has('username') && !empty($this->request->get('username'))) {
			$username = UserProfile::select('username')
				->where('user_id', '!=', $this->user()->id)
				->where('username', $this->request->get('username'))
				->get();

			return (bool)$username->count();
		}
		
		return false;
	}
}
