<?php

namespace App\Http\Requests;

use App\Models\SiteWideMessage;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteWideMessage extends FormRequest
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
			'slug' => 'string|nullable',
			'message' => 'string|nullable',
			'start_date' => 'integer|nullable',
			'end_date' => 'integer|nullable',
		];
	}
}
