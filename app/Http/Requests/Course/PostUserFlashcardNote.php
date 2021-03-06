<?php

namespace App\Http\Requests\Course;

use App\Models\UserFlashcardNote;
use Illuminate\Foundation\Http\FormRequest;

class PostUserFlashcardNote extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
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
			'note'   => 'required|string',
		];
	}
}
