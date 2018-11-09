<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFlashcardsSet extends FormRequest
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
			'name'   => 'required|string',
			'description'   => 'string',
			'mind_maps_text'   => 'string',
			'lesson_id' => 'required|integer'
		];
	}
}
