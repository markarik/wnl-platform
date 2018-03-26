<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\UserLessonAvailability;

class UpdateUserLessonAvailability extends FormRequest
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
			'date'   => 'string|required',
		];
	}
}
