<?php


namespace App\Http\Requests\User;


use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserSettings extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		$user = User::fetch($this->route('id'));

		if (!$user->settings) {
			return $user->id === $this->user()->id;
		}

		return $this->user()->can('update', $user->settings);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [

		];
	}
}