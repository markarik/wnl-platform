<?php

namespace App\Http\Requests;

use App\Models\SiteWideMessage;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteWideMessageRead extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		$user = \Auth::user();
		$siteWideMessage = SiteWideMessage::find($this->route('messageId'));

		return $siteWideMessage->user_id === $user->id;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'read_at' => 'date|nullable',
		];
	}
}
