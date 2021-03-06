<?php

namespace App\Http\Requests\Qna;

use Illuminate\Foundation\Http\FormRequest;

class PostAnswer extends FormRequest
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
			'text'        => 'required|string',
			'question_id' => 'required|numeric',
		];
	}
}
