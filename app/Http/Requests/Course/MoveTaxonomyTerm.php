<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class MoveTaxonomyTerm extends FormRequest {
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
			'term_id' => 'required|numeric|exists:taxonomy_terms,id',
			'direction' => 'required|numeric',
		];
	}
}