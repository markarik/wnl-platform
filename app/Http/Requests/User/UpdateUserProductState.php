<?php

namespace App\Http\Requests\User;

use App\Models\User;
use App\Models\UserProductState;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserProductState extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		$user = User::fetch($this->route('id'));

		return $this->user()->id === $user->id;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'wizard_step' => [
				'required',
				Rule::in([
					UserProductState::WIZARD_STEP_FINAL,
					UserProductState::WIZARD_STEP_FINISHED,
					UserProductState::WIZARD_STEP_LEARNING_STYLE,
					UserProductState::WIZARD_STEP_SATISFACTION_GUARANTEE,
					UserProductState::WIZARD_STEP_TUTORIAL,
					UserProductState::WIZARD_STEP_USER_PLAN,
					UserProductState::WIZARD_STEP_WELCOME,
				]),
			],
		];
	}
}
