<?php

namespace App\Http\Requests;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class UpdateComment extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		$comment = Comment::find($this->route('id'));

		if ($this->user()->isAdmin()) {
			return true;
		}

		return $this->user()->can('update', $comment);
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
			'verified' => 'boolean',
		];
	}
}
