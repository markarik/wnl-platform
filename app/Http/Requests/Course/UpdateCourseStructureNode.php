<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseStructureNode extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->isAdmin();
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'parent_id' => 'nullable|numeric|exists:course_structure_nodes,id',
			'structurable_id' => 'required|numeric|morph_exists:structurable_type',
			'structurable_type' => 'required|string',
			'course_id' => 'required|numeric|exists:courses,id',
			'description' => 'string|max:1000|nullable',
		];
	}
}
