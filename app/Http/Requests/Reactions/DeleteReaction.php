<?php

namespace App\Http\Requests\Reactions;

use Illuminate\Foundation\Http\FormRequest;

class DeleteReaction extends FormRequest
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
			'reactable_resource' => 'required|string',
			'reactable_id'       => 'required|numeric',
			'reaction_type'      => 'required|string',
		];
	}
}
