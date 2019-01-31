<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxonomyTerm extends FormRequest {
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
			'parent_id' => 'nullable|numeric|exists:taxonomy_terms,id',
			'tag_id' => 'required|numeric|exists:tags,id',
			'taxonomy_id' => 'required|numeric|exists:taxonomies,id',
			'description' => 'string|max:1000|nullable',
		];
	}
}
