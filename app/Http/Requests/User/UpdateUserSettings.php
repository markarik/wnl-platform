<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserSettings extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		$user = User::fetch($this->route('id'));

		return $this->user()->can('update', $user->settings);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'consent_newsletter'  => '',
			'consent_account'     => '',
			'consent_order'       => '',
			'consent_terms'       => '',
			'notifications_email' => '',
			'notifications_sms'   => '',
		];
	}
}
