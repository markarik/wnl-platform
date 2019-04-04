<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourse extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return $this->user()->isAdmin();
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'name' => 'required|string',
			'entry_exam_lesson_id' => 'exists:lessons,id',
			'entry_exam_tag_id' => 'exists:tags,id',
			'is_plan_builder_enabled' => 'boolean',
		];
	}
}
