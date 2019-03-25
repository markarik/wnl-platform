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
			'onboarding_step' => [
				'required',
				Rule::in([
					UserProductState::ONBOARDING_STEP_FINAL,
					UserProductState::ONBOARDING_STEP_FINISHED,
					UserProductState::ONBOARDING_STEP_LEARNING_STYLE,
					UserProductState::ONBOARDING_STEP_SATISFACTION_GUARANTEE,
					UserProductState::ONBOARDING_STEP_TUTORIAL,
					UserProductState::ONBOARDING_STEP_USER_PLAN,
					UserProductState::ONBOARDING_STEP_WELCOME,
				]),
			],
		];
	}
}
