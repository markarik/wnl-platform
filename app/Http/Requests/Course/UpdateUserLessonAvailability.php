<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\UserLessonAvailability;

class UpdateUserLessonAvailability extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		$lessonAvailability = UserLessonAvailability::find($this->route('id'));

		return $this->user()->can('update', $lessonAvailability);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'date'   => 'string',
		];
	}
}
