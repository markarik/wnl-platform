<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\UserLesson;

class UpdateLessonsBatch extends FormRequest
{
	public function authorize()
	{
		$routeUserId = $this->route()->userId;
		$userId = $this->user()->id;

		if ((int)$routeUserId === $userId) {
			return true;
		} else if ($this->user()->isAdmin()) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'timezone' => 'string|required',
			'manual_start_dates' => 'array|required',
		];
	}
}
