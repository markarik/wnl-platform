<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxonomyTermables extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->isAdmin() || $this->user()->isModerator();
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'annotations.*' => 'numeric|exists:annotations,id',
			'flashcards.*' => 'numeric|exists:flashcards,id',
			'quiz_questions.*' => 'numeric|exists:quiz_questions,id',
			'slides.*' => 'numeric|exists:slides,id',
		];
	}
}
