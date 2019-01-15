<?php

namespace App\Http\Requests\Qna;

use Illuminate\Foundation\Http\FormRequest;

class PostQuestion extends FormRequest
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
			'text'    => 'string|required',
			'tags'    => 'required',
			'context' => 'required',
			'discussion_id' => 'required|numeric|exists:discussions,id'
		];
	}
}
