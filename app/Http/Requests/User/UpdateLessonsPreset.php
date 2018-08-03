<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\UserLesson;

class UpdateLessonsPreset extends FormRequest
{
	public function authorize()
	{
		$routeUserId = $this->route()->userId;
		$userId = $this->user()->id;

		return (int)$routeUserId === $userId || $this->user()->isAdmin();
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'work_load' => 'numeric|nullable',
			'start_date' => 'date|nullable',
			'end_date' => 'date|nullable',
			'timezone' => 'string|required',
			'work_days' => 'array|nullable|between:1,7',
			'preset_active' => 'string|required',
		];
	}
}
