<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class MoveTaggable extends FormRequest {
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
			'target_tag_id' => 'required|numeric|exists:tags,id',
		];
	}
}
