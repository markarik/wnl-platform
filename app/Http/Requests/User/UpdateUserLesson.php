<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\UserLesson;

class UpdateUserLesson extends FormRequest
{
	public function authorize()
	{
		$user = $this->user();
		if ($user->isAdmin()) {
			return true;
		}

		return $user->id === (int) \Request::route('userId');
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'date' => 'string|required',
		];
	}
}
