<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\UserLesson;

class UpdateLessonsPreset extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'user_id' => 'integer|required',
			'workdays' => 'numeric|required',
		];
	}
}
