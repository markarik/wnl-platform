<?php

namespace App\Http\Requests\Course;

use App\Models\Taxonomy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaxonomy extends FormRequest {
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
		$taxonomy = Taxonomy::find($this->route('id'));

		return [
			'name' => ['required','string', 'max:255', Rule::unique('taxonomies')->ignore($taxonomy->id)],
			'description' => 'string|max:1000|nullable',
			'color' => 'string|max:7|nullable'
		];
	}
}
