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
			'end_date' => 'required|string',
			'user_id' => 'required|max:255',
			'days_per_lesson' => 'required|max:255',
		];
	}
}
