<?php

namespace App\Http\Requests\Qna;

use App\Models\QnaQuestion;
use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestion extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		$qnaQuestion = QnaQuestion::find($this->route('id'));

		if ($this->user()->isAdmin()) {
			return true;
		}

		return $this->user()->can('update', $qnaQuestion);
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
			'resolved' => 'boolean',
			'verified' => 'boolean'
		];
	}
}
