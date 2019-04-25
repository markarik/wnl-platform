<?php

namespace App\Http\Requests\Qna;

use App\Models\QnaAnswer;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAnswer extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		$qnaAnswer = QnaAnswer::find($this->route('id'));

		return $this->user()->can('update', $qnaAnswer);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'text' => 'string',
			'verified' => 'boolean'
		];
	}
}
