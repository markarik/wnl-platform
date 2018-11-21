<?php

namespace App\Http\Requests\Course;

use App\Models\UserFlashcardNote;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUserFlashcardNote extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		$userFlashcardNote = UserFlashcardNote::find($this->route('userFlashcardNoteId'));

		return $userFlashcardNote->user_id === Auth::user()->id;
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
