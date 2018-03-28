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

		// dd($this->request->get('userId'));
		return $user->id == $this->request->get('userId');
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
